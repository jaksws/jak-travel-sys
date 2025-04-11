<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\Quote;
use App\Models\Transaction;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PaymentService
{
    protected $validator;

    /**
     * Constructor
     *
     * @param PaymentServiceValidator $validator
     */
    public function __construct(PaymentServiceValidator $validator)
    {
        $this->validator = $validator;
    }

    /**
     * معالجة الدفع الجديد
     *
     * @param \App\Models\User $user المستخدم الذي ينفذ عملية الدفع
     * @param Quote $quote عرض السعر المرتبط بالدفع
     * @param array $data بيانات الدفع
     * @return array مصفوفة تحتوي على نتيجة المعالجة
     */
    public function processPayment($user, Quote $quote, array $data)
    {
        try {
            // التحقق من بيانات الدفع
            if (!$this->validator->validate($data)) {
                return [
                    'success' => false,
                    'errors' => $this->validator->getErrors(),
                    'message' => 'بيانات الدفع غير صالحة'
                ];
            }

            // إنشاء سجل الدفع
            $payment = new Payment([
                'payment_id' => Str::uuid(),
                'quote_id' => $quote->id,
                'user_id' => $user->id,
                'amount' => $quote->price,
                'currency_code' => $quote->currency_code ?? 'SAR',
                'payment_method' => $data['payment_method'],
                'status' => 'pending'
            ]);
            $payment->save();

            // معالجة الدفع حسب طريقة الدفع
            $paymentResult = $this->processPaymentWithGateway(
                $data['payment_method'],
                $data,
                $quote,
                $payment
            );

            if ($paymentResult['success']) {
                $payment->update([
                    'status' => 'completed',
                    'transaction_id' => $paymentResult['transaction_id'] ?? null,
                    'completed_at' => now()
                ]);

                // تحديث حالة عرض السعر
                $quote->update(['status' => 'paid']);

                // Create transaction record
                $transaction = Transaction::create([
                    'user_id' => $user->id,
                    'quote_id' => $quote->id,
                    'amount' => $quote->price, 
                    'currency_id' => $quote->currency_id,
                    'status' => 'completed',
                    'payment_method' => $data['payment_method'],
                    'reference_id' => $paymentResult['transaction_id'] ?? Str::uuid(),
                    'description' => 'دفع قيمة الخدمة'
                ]);

                return [
                    'success' => true,
                    'payment' => $payment,
                    'transaction_id' => $transaction->reference_id,
                    'message' => __('V1.payment_success') ?? 'تمت عملية الدفع بنجاح'
                ];
            } else {
                $payment->update([
                    'status' => 'failed',
                    'error_message' => $paymentResult['error_message'] ?? __('V1.payment_failed')
                ]);

                return [
                    'success' => false,
                    'payment' => $payment,
                    'message' => $paymentResult['error_message'] ?? __('V1.payment_failed')
                ];
            }
        } catch (\Exception $e) {
            Log::error('Payment processing error: ' . $e->getMessage());
            
            if (isset($payment)) {
                $payment->update([
                    'status' => 'failed',
                    'error_message' => $e->getMessage()
                ]);
            }

            return [
                'success' => false,
                'message' => __('V1.payment_failed') . ': ' . $e->getMessage()
            ];
        }
    }

    /**
     * معالجة الدفع من خلال البوابة المناسبة
     */
    private function processPaymentWithGateway($gateway, $paymentData, $quote, $payment)
    {
        // Always return success in test environment
        if (app()->environment('testing') || config('V1_features.payment_system.test_mode')) {
            return [
                'success' => true,
                'transaction_id' => Str::random(16),
                'message' => 'Test payment processed successfully',
            ];
        }

        switch ($gateway) {
            case 'mada':
                return $this->processMadaPayment($paymentData, $quote, $payment);
            case 'credit_card':
                return $this->processCreditCardPayment($paymentData, $quote, $payment);
            // ... معالجة طرق الدفع الأخرى
            default:
                throw new \Exception(__('V1.invalid_payment_method'));
        }
    }

    /**
     * استرداد مبلغ معاملة
     *
     * @param Transaction $transaction المعاملة المراد استرداد مبلغها
     * @param string $reason سبب الاسترداد
     * @return array نتيجة عملية الاسترداد
     */
    public function refundPayment(Transaction $transaction, string $reason = '')
    {
        try {
            // التحقق من أن المعاملة قابلة للاسترداد
            if ($transaction->status !== 'completed') {
                return [
                    'success' => false,
                    'message' => 'لا يمكن استرداد مبلغ معاملة غير مكتملة'
                ];
            }

            // في البيئة التجريبية، نحاكي عملية استرداد ناجحة دائمًا
            if (app()->environment('testing') || config('V1_features.payment_system.test_mode')) {
                $transaction->update([
                    'status' => 'refunded',
                    'refund_reason' => $reason,
                    'refunded_at' => now()
                ]);

                return [
                    'success' => true,
                    'message' => 'تم استرداد المبلغ بنجاح',
                    'transaction' => $transaction
                ];
            }

            // استرداد المبلغ حسب طريقة الدفع
            $refundResult = $this->processRefundWithGateway($transaction);

            if ($refundResult['success']) {
                $transaction->update([
                    'status' => 'refunded',
                    'refund_reason' => $reason,
                    'refunded_at' => now(),
                    'refund_reference' => $refundResult['refund_id'] ?? null
                ]);

                // تحديث حالة عرض السعر إذا كان موجودًا
                if ($transaction->quote) {
                    $transaction->quote->update(['status' => 'refunded']);
                }

                return [
                    'success' => true,
                    'message' => 'تم استرداد المبلغ بنجاح',
                    'transaction' => $transaction
                ];
            } else {
                return [
                    'success' => false,
                    'message' => $refundResult['error_message'] ?? 'فشلت عملية استرداد المبلغ',
                    'transaction' => $transaction
                ];
            }
        } catch (\Exception $e) {
            Log::error('Refund processing error: ' . $e->getMessage());
            
            return [
                'success' => false,
                'message' => 'حدث خطأ أثناء استرداد المبلغ: ' . $e->getMessage()
            ];
        }
    }

    /**
     * معالجة استرداد المبلغ من خلال البوابة المناسبة
     */
    private function processRefundWithGateway(Transaction $transaction)
    {
        // في البيئة التجريبية، نحاكي عملية استرداد ناجحة دائمًا
        if (app()->environment('testing') || config('V1_features.payment_system.test_mode')) {
            return [
                'success' => true,
                'refund_id' => 'ref_' . Str::random(16),
                'message' => 'تمت عملية الاسترداد بنجاح',
            ];
        }

        switch ($transaction->payment_method) {
            case 'mada':
                return $this->processMadaRefund($transaction);
            case 'credit_card':
                return $this->processCreditCardRefund($transaction);
            default:
                throw new \Exception('طريقة دفع غير مدعومة للاسترداد');
        }
    }

    /**
     * معالجة استرداد مبلغ بواسطة بطاقة مدى
     */
    private function processMadaRefund(Transaction $transaction)
    {
        // تنفيذ استرداد المبلغ من بوابة مدى
        try {
            // رمز لمحاكاة استرداد المبلغ
            return [
                'success' => true,
                'refund_id' => 'mada_ref_' . Str::random(16),
                'message' => 'تمت عملية الاسترداد بنجاح'
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error_message' => 'فشل استرداد المبلغ: ' . $e->getMessage()
            ];
        }
    }

    /**
     * معالجة استرداد مبلغ بواسطة بطاقة ائتمان
     */
    private function processCreditCardRefund(Transaction $transaction)
    {
        // تنفيذ استرداد المبلغ من بوابة بطاقات الائتمان
        try {
            // رمز لمحاكاة استرداد المبلغ
            return [
                'success' => true,
                'refund_id' => 'cc_ref_' . Str::random(16),
                'message' => 'تمت عملية الاسترداد بنجاح'
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error_message' => 'فشل استرداد المبلغ: ' . $e->getMessage()
            ];
        }
    }

    // ... أساليب معالجة كل طريقة دفع
}
