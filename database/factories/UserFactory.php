<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            'role' => 'user', // Default role based on migration
            'is_active' => true, // Default status based on migration
            'status' => 'active', // Align with User model's isActive() check if necessary
            'agency_id' => null,
            'city' => fake()->city(),
            'country' => fake()->country(),
            'avatar' => null,
            'id_number' => null,
            'passport_number' => null,
            'nationality' => fake()->countryCode(),
            'preferred_currency' => 'USD',
            'notification_preferences' => null,
            'locale' => 'en',
            'theme' => 'light',
            'email_notifications' => true,
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    /**
     * Indicate that the user is an admin.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function admin(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'role' => 'admin',
            ];
        });
    }

    /**
     * Indicate that the user is inactive.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function inactive(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'is_active' => false,
                'status' => 'inactive', // Also update status if used by application logic
            ];
        });
    }

    /**
     * Indicate that the user is an agency/agent.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function agent(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'role' => 'agent', // Or 'agency' depending on your exact roles
            ];
        });
    }

    /**
     * Indicate that the user is a subagent.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function subagent(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'role' => 'subagent',
            ];
        });
    }

    /**
     * Indicate that the user is a client/customer.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function client(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'role' => 'client', // Or 'customer' depending on your exact roles
            ];
        });
    }
}
