<?php

namespace Database\Seeders;

use App\Models\Currency;
use Illuminate\Database\Seeder;

// Reviewed on 2023-10-01 by John Doe

class CurrencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $currencies = [
            [
                'code' => 'SAR',
                'name' => 'الريال السعودي',
                'symbol' => 'ر.س',
                'is_default' => true,
                'exchange_rate' => 1.0000,
                'status' => 'active',
            ],
            [
                'code' => 'USD',
                'name' => 'الدولار الأمريكي',
                'symbol' => '$',
                'is_default' => false,
                'exchange_rate' => 0.2667, // 1 SAR = 0.2667 USD
                'status' => 'active',
            ],
            [
                'code' => 'EUR',
                'name' => 'اليورو',
                'symbol' => '€',
                'is_default' => false,
                'exchange_rate' => 0.2453, // 1 SAR = 0.2453 EUR
                'status' => 'active',
            ],
            [
                'code' => 'YER',
                'name' => 'الريال اليمني',
                'symbol' => 'ر.ي',
                'is_default' => false,
                'exchange_rate' => 66.7500, // 1 SAR = 66.75 YER
                'status' => 'active',
            ],
        ];

        foreach ($currencies as $currency) {
            Currency::updateOrCreate(
                ['code' => $currency['code']],
                $currency
            );
        }
    }
}
