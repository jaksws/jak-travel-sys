<?php

namespace Database\Factories;

use App\Models\Agency;
use Illuminate\Database\Eloquent\Factories\Factory;

class AgencyFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Agency::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => 'وكالة ' . $this->faker->company(),
            'license_number' => 'AG' . $this->faker->unique()->numerify('#####'),
            'email' => $this->faker->unique()->safeEmail(),
            'phone' => '+966' . $this->faker->numerify('#########'),
            'address' => $this->faker->address(),
            'logo' => null,
            'website' => $this->faker->domainName(),
            'description' => $this->faker->paragraph(),
            'status' => $this->faker->randomElement(['active', 'suspended', 'pending']),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    /**
     * Configure the factory to create an active agency.
     *
     * @return $this
     */
    public function active()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'active',
            ];
        });
    }

    /**
     * Configure the factory to create a suspended agency.
     *
     * @return $this
     */
    public function suspended()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'suspended',
            ];
        });
    }
}