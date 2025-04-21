<?php

namespace App\Helpers;

use App\Models\Currency;
use App\Models\Agency;

class CurrencyHelper
{
    /**
     * تنسيق سعر حسب إعدادات الوكالة
     *
     * @param float $amount المبلغ المراد تنسيقه
     * @param string|null $currencyCode رمز العملة (إذا كان مختلفاً عن العملة الافتراضية)
     * @param Agency|null $agency الوكالة (لتطبيق إعداداتها)
     * @return string السعر المنسق
     */
    public static function formatPrice($amount, $currencyCode = null, $agency = null)
    {
        // الحصول على الوكالة إذا لم يتم تمريرها
        if (!$agency && auth()->check() && auth()->user()->agency_id) {
            $agency = auth()->user()->agency;
        }

        // تهيئة القيم الافتراضية إذا لم يتم العثور على الوكالة
        $decimals = $agency->price_decimals ?? 2;
        $format = $agency->price_display_format ?? 'symbol_first';

        // الحصول على العملة
        $currency = null;
        
        if (!empty($currencyCode)) {
            // محاولة العثور على العملة بالرمز المحدد
            $currency = Currency::where('code', $currencyCode)->first();
        }
        
        if (!$currency) {
            // إذا لم يتم تحديد عملة أو لم يتم العثور عليها، استخدم العملة الافتراضية للوكالة
            if ($agency && !empty($agency->default_currency)) {
                $currency = Currency::where('code', $agency->default_currency)->first();
            }
            
            // إذا لم يتم العثور على عملة الوكالة، استخدم العملة الافتراضية للنظام
            if (!$currency) {
                $currency = Currency::where('is_default', true)->first();
            }
            
            // إذا لم يتم العثور على أي عملة، قم بإنشاء كائن عملة افتراضي
            if (!$currency) {
                $currency = new Currency([
                    'code' => 'SAR',
                    'symbol' => 'ر.س',
                    'is_default' => true,
                    'exchange_rate' => 1.0000
                ]);
            }
        }

        // تنسيق المبلغ حسب عدد الخانات العشرية
        $formattedAmount = number_format($amount, $decimals);

        // تطبيق تنسيق عرض العملة
        switch ($format) {
            case 'symbol_first':
                // No space for USD, space for others
                return $currency->symbol . ($currency->symbol === '$' ? '' : ' ') . $formattedAmount;
            case 'symbol_last':
                return $formattedAmount . ' ' . $currency->symbol;
            case 'code_first':
                return $currency->code . ' ' . $formattedAmount;
            case 'code_last':
                return $formattedAmount . ' ' . $currency->code;
            default:
                return $formattedAmount . ' ' . $currency->symbol;
        }
    }

    /**
     * تحويل مبلغ من عملة إلى أخرى
     *
     * @param float $amount المبلغ المراد تحويله
     * @param string $fromCurrency رمز العملة المصدر
     * @param string $toCurrency رمز العملة الهدف
     * @return float المبلغ بعد التحويل
     */
    public static function convertPrice($amount, $fromCurrency, $toCurrency)
    {
        if ($fromCurrency === $toCurrency) {
            return $amount;
        }

        $sourceCurrency = Currency::where('code', $fromCurrency)->first();
        $targetCurrency = Currency::where('code', $toCurrency)->first();

        // إذا لم يتم العثور على إحدى العملات، أرجع المبلغ الأصلي
        if (!$sourceCurrency || !$targetCurrency) {
            return $amount;
        }

        // تحويل المبلغ إلى العملة الأساسية (التي لها سعر صرف = 1)
        $baseAmount = $amount / $sourceCurrency->exchange_rate;
        
        // ثم تحويله إلى العملة المستهدفة
        return $baseAmount * $targetCurrency->exchange_rate;
    }

    /**
     * تحويل بين عملتين
     *
     * @param float $amount
     * @param int $fromCurrencyId
     * @param int $toCurrencyId
     * @return float
     */
    public static function convert(float $amount, int $fromCurrencyId, int $toCurrencyId): float
    {
        $fromCurrency = Currency::findOrFail($fromCurrencyId);
        $toCurrency = Currency::findOrFail($toCurrencyId);
        
        // تحويل من العملة المصدر إلى الدولار الأمريكي كعملة وسيطة
        $valueInUSD = $amount / $fromCurrency->exchange_rate;
        
        // تحويل من الدولار الأمريكي إلى العملة الهدف
        return $valueInUSD * $toCurrency->exchange_rate;
    }
    
    /**
     * تنسيق المبلغ بعملة معينة
     *
     * @param float $amount
     * @param int $currencyId
     * @return string
     */
    public static function format(float $amount, int $currencyId): string
    {
        $currency = Currency::findOrFail($currencyId);
        $formattedAmount = number_format($amount, 2);
        
        // خاص بعملة الريال السعودي - الرمز يظهر بعد المبلغ دائمًا
        if ($currency->code === 'SAR') {
            return $formattedAmount . $currency->symbol;
        }
        
        // إذا كان رمز العملة يظهر قبل المبلغ
        if ($currency->symbol_position === 'before') {
            return $currency->symbol . $formattedAmount;
        }
        
        // إذا كان رمز العملة يظهر بعد المبلغ
        return $formattedAmount . ' ' . $currency->symbol;
    }
    
    /**
     * الحصول على العملة الافتراضية
     *
     * @return Currency
     */
    public static function getDefaultCurrency(): ?Currency
    {
        return Currency::where('is_default', true)->first();
    }
    
    /**
     * الحصول على قائمة العملات كقائمة منسدلة
     *
     * @return array
     */
    public static function getCurrencyOptions(): array
    {
        $currencies = Currency::all();
        $options = [];
        
        foreach ($currencies as $currency) {
            $options[] = [
                'value' => $currency->id,
                'label' => "{$currency->name} ({$currency->symbol})"
            ];
        }
        
        return $options;
    }
}
