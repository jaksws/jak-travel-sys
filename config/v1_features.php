<?php

return [
    /*
    |--------------------------------------------------------------------------
    | ميزات الإصدار 1.0
    |--------------------------------------------------------------------------
    |
    | هذا الملف يحدد حالة تفعيل ميزات الإصدار 1.0 من النظام
    | يمكنك تفعيل أو تعطيل أي من هذه الميزات حسب الحاجة
    |
    */

    'multilingual' => [
        'enabled' => true,
        'available_locales' => ['ar', 'en', 'fr', 'tr', 'es', 'id', 'ur'],
        'default_locale' => 'ar',
    ],

    'dark_mode' => [
        'enabled' => true,
        'default' => 'system', // 'light', 'dark', 'system'
    ],

    'payment_system' => [
        'enabled' => true,
        'providers' => [
            'mada' => [
                'enabled' => true,
                'test_mode' => true,
                'config' => [
                    'merchant_id' => env('MADA_MERCHANT_ID', ''),
                    'api_key' => env('MADA_API_KEY', ''),
                    'secret_key' => env('MADA_SECRET_KEY', ''),
                ],
            ],
            'visa' => [
                'enabled' => true,
                'test_mode' => true,
                'config' => [
                    'merchant_id' => env('VISA_MERCHANT_ID', ''),
                    'api_key' => env('VISA_API_KEY', ''),
                ],
            ],
            'mastercard' => [
                'enabled' => true,
                'test_mode' => true,
                'config' => [
                    'merchant_id' => env('MASTERCARD_MERCHANT_ID', ''),
                    'api_key' => env('MASTERCARD_API_KEY', ''),
                ],
            ],
            'apple_pay' => [
                'enabled' => true,
                'test_mode' => true,
                'config' => [
                    'merchant_id' => env('APPLE_PAY_MERCHANT_ID', ''),
                    'certificate_path' => env('APPLE_PAY_CERTIFICATE_PATH', ''),
                ],
            ],
            'google_pay' => [
                'enabled' => true,
                'test_mode' => true,
                'config' => [
                    'merchant_id' => env('GOOGLE_PAY_MERCHANT_ID', ''),
                    'api_key' => env('GOOGLE_PAY_API_KEY', ''),
                ],
            ],
        ],
    ],

    'enhanced_ui' => [
        'enabled' => true,
    ],
    
    'ai_features' => [
        'enabled' => false,
        'smart_pricing' => false,
        'customer_recommendations' => false,
        'virtual_assistant' => false,
    ],
];
