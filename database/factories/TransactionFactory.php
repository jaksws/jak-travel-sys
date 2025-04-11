<?php

namespace Database\Factories;

use App\Models\Transaction;
use App\Models\User;
use App\Models\Quote;
use App\Models\Agency;
use App\Models\Currency;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class TransactionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Transaction::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'quote_id' => Quote::factory(),
            'agency_id' => null, // Make this nullable
            'currency_id' => Currency::factory(),
            'amount' => $this->faker->numberBetween(100, 10000),
            'status' => $this->faker->randomElement(['pending', 'completed', 'failed', 'refunded']),
            'payment_method' => $this->faker->randomElement(['credit_card', 'mada', 'bank_transfer']),
            'reference_id' => 'txn_' . Str::random(16),
            'description' => $this->faker->sentence(),
            'type' => $this->faker->randomElement(['payment', 'refund', 'commission']),
            'refunded_at' => null,
            'refund_reason' => null,
            'refund_reference' => null,
        ];
    }

    /**
     * Indicate that the transaction is completed.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function completed()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'completed',
                'type' => 'payment'
            ];
        });
    }

    /**
     * Indicate that the transaction is refunded.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function refunded()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'refunded',
                'type' => 'refund',
                'refunded_at' => now(),
                'refund_reason' => 'Customer request',
                'refund_reference' => 'ref_' . Str::random(16),
            ];
        });
    }
}