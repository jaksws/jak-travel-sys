<?php

namespace Database\Factories;

use App\Models\Currency;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\DB;

class CurrencyFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Currency::class;

    // Static property to keep track of used codes
    protected static $usedCodes = [];
    
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $currencies = [
            'SAR' => ['name' => 'ريال سعودي', 'symbol' => '﷼', 'exchange_rate' => 1.0],
            'USD' => ['name' => 'دولار أمريكي', 'symbol' => '$', 'exchange_rate' => 0.27],
            'EUR' => ['name' => 'يورو', 'symbol' => '€', 'exchange_rate' => 0.24],
            'EGP' => ['name' => 'جنيه مصري', 'symbol' => 'ج.م', 'exchange_rate' => 8.3],
            'AED' => ['name' => 'درهم إماراتي', 'symbol' => 'د.إ', 'exchange_rate' => 0.98],
            'GBP' => ['name' => 'جنيه إسترليني', 'symbol' => '£', 'exchange_rate' => 0.21],
            'JPY' => ['name' => 'ين ياباني', 'symbol' => '¥', 'exchange_rate' => 39.5],
            'KWD' => ['name' => 'دينار كويتي', 'symbol' => 'د.ك', 'exchange_rate' => 0.082],
            'BHD' => ['name' => 'دينار بحريني', 'symbol' => 'د.ب', 'exchange_rate' => 0.10],
            'QAR' => ['name' => 'ريال قطري', 'symbol' => 'ر.ق', 'exchange_rate' => 0.97],
            'OMR' => ['name' => 'ريال عماني', 'symbol' => 'ر.ع', 'exchange_rate' => 0.10],
            'JOD' => ['name' => 'دينار أردني', 'symbol' => 'د.ا', 'exchange_rate' => 0.19],
        ];
        
        // First check which codes already exist in the database
        $existingCodes = DB::table('currencies')->pluck('code')->toArray();
        
        // Merge with our static list of used codes in this factory run
        $unavailableCodes = array_merge($existingCodes, static::$usedCodes);
        
        // Filter out the currencies that are already in use
        $availableCurrencies = array_diff_key($currencies, array_flip($unavailableCodes));
        
        if (empty($availableCurrencies)) {
            // If we've used all standard currencies, generate a random unique one
            $code = 'CUR' . strtoupper($this->faker->unique()->lexify('???'));
            $currencyInfo = [
                'name' => 'Custom Currency ' . $code,
                'symbol' => $code,
                'exchange_rate' => $this->faker->randomFloat(2, 0.1, 10)
            ];
        } else {
            // Otherwise pick from the available currencies
            $code = $this->faker->randomElement(array_keys($availableCurrencies));
            $currencyInfo = $currencies[$code];
        }
        
        // Remember we've used this code
        static::$usedCodes[] = $code;
        
        return [
            'code' => $code,
            'name' => $currencyInfo['name'],
            'symbol' => $currencyInfo['symbol'],
            'exchange_rate' => $currencyInfo['exchange_rate'],
            'is_default' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    /**
     * Configure the factory to create the default currency.
     *
     * @return $this
     */
    public function default()
    {
        return $this->state(function (array $attributes) {
            // Check if SAR already exists in the database
            $sarExists = DB::table('currencies')->where('code', 'SAR')->exists();
            
            // If it exists, generate a different default currency
            if ($sarExists) {
                return [
                    'is_default' => true,
                    'code' => 'USD',
                    'name' => 'دولار أمريكي',
                    'symbol' => '$',
                    'exchange_rate' => 0.27,
                ];
            }
            
            return [
                'is_default' => true,
                'code' => 'SAR',
                'name' => 'ريال سعودي',
                'symbol' => '﷼',
                'exchange_rate' => 1.0,
            ];
        });
    }
}