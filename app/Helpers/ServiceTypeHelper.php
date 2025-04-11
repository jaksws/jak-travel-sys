<?php

namespace App\Helpers;

class ServiceTypeHelper
{
    // أنواع الخدمات المعتمدة في النظام
    const TYPES = [
        'flight_ticket' => 'تذاكر طيران',
        'hotel' => 'حجز فندق',
        'package' => 'باقة سياحية',
        'visa' => 'تأشيرة',
        'transport' => 'نقل ومواصلات',
        'activity' => 'نشاط سياحي',
        'insurance' => 'تأمين سفر'
    ];
    
    /**
     * الحصول على قائمة أنواع الخدمات
     *
     * @return array
     */
    public static function getTypes(): array
    {
        return self::TYPES;
    }
    
    /**
     * الحصول على اسم نوع الخدمة بالعربية
     *
     * @param string $type
     * @return string|null
     */
    public static function getTypeName(string $type): ?string
    {
        return self::TYPES[$type] ?? null;
    }
    
    /**
     * التحقق من صلاحية نوع الخدمة
     *
     * @param string $type
     * @return bool
     */
    public static function isValidType(string $type): bool
    {
        return array_key_exists($type, self::TYPES);
    }
    
    /**
     * الحصول على قائمة أنواع الخدمات كقائمة منسدلة
     *
     * @return array
     */
    public static function getTypeOptions(): array
    {
        $options = [];
        foreach (self::TYPES as $key => $value) {
            $options[] = [
                'value' => $key,
                'label' => $value
            ];
        }
        return $options;
    }
    
    /**
     * الحصول على رمز لنوع الخدمة
     *
     * @param string $type
     * @return string
     */
    public static function getTypeIcon(string $type): string
    {
        $icons = [
            'flight_ticket' => 'fa-plane',
            'hotel' => 'fa-hotel',
            'package' => 'fa-suitcase',
            'visa' => 'fa-passport',
            'transport' => 'fa-car',
            'activity' => 'fa-hiking',
            'insurance' => 'fa-shield-alt'
        ];
        
        return $icons[$type] ?? 'fa-question';
    }
}
