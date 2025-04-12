<?php

namespace Database\Factories;

use App\Models\Payment;
use App\Models\User;
use App\Models\Quote;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Payment>
 */
class PaymentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'payment_id' => 'PAY_' . uniqid(),
            'quote_id' => Quote::factory(),
            'user_id' => User::factory(),
            'amount' => $this->faker->randomFloat(2, 100, 10000),
            'currency_code' => $this->faker->randomElement(['SAR', 'USD', 'EUR', 'AED']),
            'payment_method' => $this->faker->randomElement(['credit_card', 'bank_transfer', 'cash', 'mada']),
            'status' => $this->faker->randomElement(['pending', 'completed', 'failed', 'refunded']),
            'transaction_id' => 'TXN_' . uniqid(),
            'error_message' => null,
            'completed_at' => $this->faker->boolean(70) ? now() : null
        ];
    }

    /**
     * تعيين حالة الدفع على "مكتمل"
     */
    public function completed()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'completed',
                'completed_at' => now(),
                'error_message' => null,
            ];
        });
    }

    /**
     * تعيين حالة الدفع على "فاشل"
     */
    public function failed()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'failed',
                'completed_at' => null,
                'error_message' => 'فشل عملية الدفع: رفض البنك المعاملة',
            ];
        });
    }

    /**
     * تعيين حالة الدفع على "قيد الانتظار"
     */
    public function pending()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'pending',
                'completed_at' => null,
                'error_message' => null,
            ];
        });
    }
}