<?php

namespace App\Services;

use Illuminate\Support\Facades\Schema;

class PaymentServiceValidator 
{
    public static function validateDatabaseStructure(): array
    {
        $issues = [];
        
        // التحقق من وجود جدول المدفوعات
        if (!Schema::hasTable('payments')) {
            $issues[] = 'جدول المدفوعات غير موجود';
        } else {
            // التحقق من وجود الأعمدة المطلوبة
            $requiredColumns = [
                'id', 'quote_id', 'amount', 'currency_id', 'payment_date',
                'payment_method', 'status', 'transaction_reference'
            ];
            
            foreach ($requiredColumns as $column) {
                if (!Schema::hasColumn('payments', $column)) {
                    $issues[] = "العمود {$column} غير موجود في جدول المدفوعات";
                }
            }
        }
        
        // التحقق من وجود جدول العملات
        if (!Schema::hasTable('currencies')) {
            $issues[] = 'جدول العملات غير موجود';
        }
        
        // التحقق من وجود جدول عروض الأسعار
        if (!Schema::hasTable('quotes')) {
            $issues[] = 'جدول عروض الأسعار غير موجود';
        }
        
        return $issues;
    }
}
