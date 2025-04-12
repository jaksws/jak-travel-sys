<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'symbol',
        'exchange_rate',
        'is_default',
    ];

    protected $casts = [
        'exchange_rate' => 'float',
        'is_default' => 'boolean',
    ];

    /**
     * The "booted" method of the model.
     */
    protected static function booted()
    {
        static::creating(function ($currency) {
            if ($currency->is_default) {
                static::where('is_default', true)->update(['is_default' => false]);
            }
        });

        static::updating(function ($currency) {
            if ($currency->is_default && $currency->isDirty('is_default')) {
                static::where('is_default', true)->where('id', '!=', $currency->id)->update(['is_default' => false]);
            }
        });
    }

    /**
     * التحقق من أن العملة هي العملة الافتراضية
     */
    public function isDefault()
    {
        return $this->is_default;
    }

    /**
     * تنسيق المبلغ بناءً على العملة
     *
     * @param float $amount
     * @return string
     */
    public function format($amount)
    {
        $formattedAmount = number_format($amount, 2);
        
        // تحديد موضع رمز العملة (قبل أو بعد)
        $symbolPosition = $this->getSymbolPosition();
        
        if ($symbolPosition === 'before') {
            return $this->symbol . $formattedAmount;
        } else {
            return $formattedAmount . ' ' . $this->symbol;
        }
    }

    /**
     * الحصول على موضع رمز العملة
     *
     * @return string
     */
    protected function getSymbolPosition()
    {
        // تحديد موضع الرمز بناءً على رمز العملة
        if ($this->code === 'USD' || $this->code === 'EUR' || $this->code === 'GBP') {
            return 'before';
        }
        
        return 'after';
    }

    /**
     * تحويل المبلغ من هذه العملة إلى عملة أخرى
     *
     * @param float $amount
     * @param Currency $targetCurrency
     * @return float
     */
    public function convertTo($amount, Currency $targetCurrency)
    {
        // إذا كانت العملة المصدر هي نفسها العملة الهدف
        if ($this->id === $targetCurrency->id) {
            return $amount;
        }

        // معدل تحويل العملة المصدر بالنسبة للعملة الأساسية
        $sourceRate = $this->exchange_rate;
        
        // معدل تحويل العملة الهدف بالنسبة للعملة الأساسية
        $targetRate = $targetCurrency->exchange_rate;

        // التحويل إلى العملة الأساسية أولاً ثم إلى العملة الهدف
        $convertedAmount = ($amount / $sourceRate) * $targetRate;
        
        // تقريب المبلغ لأقرب رقمين عشريين
        return round($convertedAmount, 2);
    }

    /**
     * الحصول على العملة الافتراضية في النظام
     *
     * @return Currency
     */
    public static function getDefault()
    {
        $default = static::where('is_default', true)->first();
        
        if (!$default) {
            // إذا لم تكن هناك عملة افتراضية، استخدم الريال السعودي أو أول عملة
            $default = static::where('code', 'SAR')->first() ?? static::first();
            
            // تعيين هذه العملة كعملة افتراضية
            if ($default) {
                $default->update(['is_default' => true]);
            }
        }
        
        return $default;
    }
}
