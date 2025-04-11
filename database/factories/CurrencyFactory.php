<?php

namespace Database\Factories;

use App\Models\Currency;
use Illuminate\Database\Eloquent\Factories\Factory;

class CurrencyFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Currency::class;

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
        ];
        
        $code = $this->faker->randomElement(array_keys($currencies));
        $currencyInfo = $currencies[$code];
        
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