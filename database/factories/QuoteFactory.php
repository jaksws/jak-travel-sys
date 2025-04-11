<?php

namespace Database\Factories;

use App\Models\Quote;
use App\Models\User;
use App\Models\Request;
use App\Models\Currency;
use Illuminate\Database\Eloquent\Factories\Factory;

class QuoteFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Quote::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'request_id' => Request::factory(),
            'user_id' => User::factory(),
            'price' => $this->faker->numberBetween(1000, 10000),
            'currency_id' => Currency::factory(),
            'description' => $this->faker->paragraph(),
            'status' => $this->faker->randomElement(['pending', 'accepted', 'rejected', 'expired']),
            'valid_until' => $this->faker->dateTimeBetween('+1 week', '+1 month'),
            'notes' => $this->faker->optional()->sentence(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    /**
     * Configure the factory to create a pending quote.
     *
     * @return $this
     */
    public function pending()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'pending',
            ];
        });
    }

    /**
     * Configure the factory to create an accepted quote.
     *
     * @return $this
     */
    public function accepted()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'accepted',
            ];
        });
    }

    /**
     * Configure the factory to create a quote for a specific request.
     *
     * @param int $requestId
     * @return $this
     */
    public function forRequest($requestId)
    {
        return $this->state(function (array $attributes) use ($requestId) {
            return [
                'request_id' => $requestId,
            ];
        });
    }
}