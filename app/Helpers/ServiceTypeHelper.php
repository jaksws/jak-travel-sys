<?php

namespace App\Helpers;

class ServiceTypeHelper
{
    // Constants for service types
    const HAJJ = 'hajj';
    const UMRAH = 'umrah';
    const VISA = 'visa';
    const FLIGHT_TICKET = 'flight_ticket';
    const HOTEL = 'hotel';
    const TRANSPORT = 'transport';
    const PACKAGE = 'package';
    const ACTIVITY = 'activity';
    const INSURANCE = 'insurance';

    // أنواع الخدمات المعتمدة في النظام
    const TYPES = [
        self::FLIGHT_TICKET => 'تذاكر طيران',
        self::HOTEL => 'حجز فندق',
        self::PACKAGE => 'باقة سياحية',
        self::VISA => 'تأشيرة',
        self::TRANSPORT => 'نقل ومواصلات',
        self::ACTIVITY => 'نشاط سياحي',
        self::INSURANCE => 'تأمين سفر',
        self::HAJJ => 'حج',
        self::UMRAH => 'عمرة',
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
            'insurance' => 'fa-shield-alt',
            'hajj' => 'fa-kaaba',
            'umrah' => 'fa-kaaba'
        ];
        
        return $icons[$type] ?? 'fa-question';
    }
}
