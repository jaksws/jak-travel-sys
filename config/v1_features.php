<?php

return [
    'multilingual' => false,
    'dark_mode' => false,
    'payment_system' => false,
    'enhanced_ui' => false,
    'ai_features' => false,
    'backup' => array (
  'multilingual' => 
  array (
    'enabled' => false,
    'available_locales' => 
    array (
      0 => 'ar',
      1 => 'en',
      2 => 'fr',
      3 => 'tr',
    ),
    'default_locale' => 'ar',
  ),
  'dark_mode' => 
  array (
    'enabled' => false,
    'default' => 'light',
  ),
  'payment_system' => 
  array (
    'enabled' => false,
    'providers' => 
    array (
      'mada' => 
      array (
        'enabled' => false,
        'test_mode' => true,
        'config' => 
        array (
          'merchant_id' => '',
          'api_key' => '',
          'secret_key' => '',
        ),
      ),
      'visa' => 
      array (
        'enabled' => false,
        'test_mode' => true,
        'config' => 
        array (
          'merchant_id' => '',
          'api_key' => '',
        ),
      ),
      'mastercard' => 
      array (
        'enabled' => false,
        'test_mode' => true,
        'config' => 
        array (
          'merchant_id' => '',
          'api_key' => '',
        ),
      ),
      'apple_pay' => 
      array (
        'enabled' => false,
        'test_mode' => true,
        'config' => 
        array (
          'merchant_id' => '',
          'certificate_path' => '',
        ),
      ),
      'google_pay' => 
      array (
        'enabled' => false,
        'test_mode' => true,
        'config' => 
        array (
          'merchant_id' => '',
          'api_key' => '',
        ),
      ),
    ),
  ),
  'enhanced_ui' => 
  array (
    'enabled' => true,
  ),
),
];
