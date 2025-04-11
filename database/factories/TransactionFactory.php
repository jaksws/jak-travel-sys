<?php

namespace Database\Factories;

use App\Models\Transaction;
use App\Models\User;
use App\Models\Quote;
use App\Models\Currency;
use Illuminate\Database\Eloquent\Factories\Factory;

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
            'amount' => $this->faker->numberBetween(1000, 10000),
            'currency_id' => Currency::factory(),
            'reference_id' => 'txn_' . $this->faker->unique()->uuid(),
            'payment_method' => $this->faker->randomElement(['credit_card', 'bank_transfer', 'cash', 'online_payment']),
            'status' => $this->faker->randomElement(['completed', 'pending', 'failed', 'refunded']),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    /**
     * Configure the factory to create a completed transaction.
     *
     * @return $this
     */
    public function completed()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'completed',
            ];
        });
    }

    /**
     * Configure the factory to create a failed transaction.
     *
     * @return $this
     */
    public function failed()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'failed',
            ];
        });
    }

    /**
     * Configure the factory to create a transaction for a specific quote.
     *
     * @param int $quoteId
     * @return $this
     */
    public function forQuote($quoteId)
    {
        return $this->state(function (array $attributes) use ($quoteId) {
            return [
                'quote_id' => $quoteId,
            ];
        });
    }
}