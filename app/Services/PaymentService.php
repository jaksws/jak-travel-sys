<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\Quote;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PaymentService
{
    /**
     * معالجة الدفع الجديد
     *
     * @param array $data بيانات الدفع
     * @param Quote $quote عرض السعر المرتبط بالدفع
     * @return array مصفوفة تحتوي على نتيجة المعالجة
     */
    public function processPayment(array $data, Quote $quote)
    {
        try {
            // إنشاء سجل الدفع
            $payment = new Payment([
                'payment_id' => Str::uuid(),
                'quote_id' => $quote->id,
                'user_id' => auth()->id(),
                'amount' => $quote->price,
                'currency_code' => $quote->currency_code,
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

                return [
                    'success' => true,
                    'payment' => $payment,
                    'message' => __('V1.payment_success')
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
        // في وضع الاختبار، نحاكي عملية دفع ناجحة
        if (config('V1_features.payment_system.test_mode')) {
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

    // ... أساليب معالجة كل طريقة دفع
}
