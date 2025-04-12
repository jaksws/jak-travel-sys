<?php

namespace Database\Factories;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Notification>
 */
class NotificationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'title' => $this->faker->sentence(4),
            'message' => $this->faker->paragraph(),
            'type' => $this->faker->randomElement(['quote_accepted', 'quote_rejected', 'request_created', 'payment_received']),
            'is_read' => $this->faker->boolean(30),
            'data' => json_encode(['quote_id' => rand(1, 100), 'request_id' => rand(1, 100)]),
            'link' => '/quotes/' . rand(1, 100)
        ];
    }

    /**
     * تعيين الحالة على "غير مقروء"
     */
    public function unread()
    {
        return $this->state(function (array $attributes) {
            return [
                'is_read' => false,
            ];
        });
    }

    /**
     * تعيين الحالة على "مقروء"
     */
    public function read()
    {
        return $this->state(function (array $attributes) {
            return [
                'is_read' => true,
            ];
        });
    }
}