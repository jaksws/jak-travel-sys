<?php

namespace Database\Factories;

use App\Models\Request;
use App\Models\User;
use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;

class RequestFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Request::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $titles = [
            'طلب حجز رحلة عمرة',
            'طلب تأشيرة زيارة',
            'استفسار عن رحلة الحج',
            'طلب حجز تذاكر طيران',
            'طلب خدمة نقل',
        ];
        
        return [
            'user_id' => User::factory(),
            'service_id' => Service::factory(),
            'title' => $this->faker->randomElement($titles),
            'description' => $this->faker->paragraph(),
            'status' => $this->faker->randomElement(['pending', 'approved', 'rejected', 'completed']),
            'required_date' => $this->faker->dateTimeBetween('+1 week', '+3 months'),
            'notes' => $this->faker->optional()->sentence(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    /**
     * Configure the factory to create a pending request.
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
     * Configure the factory to create an approved request.
     *
     * @return $this
     */
    public function approved()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'approved',
            ];
        });
    }

    /**
     * Configure the factory to create a request for a specific user.
     *
     * @param int $userId
     * @return $this
     */
    public function forUser($userId)
    {
        return $this->state(function (array $attributes) use ($userId) {
            return [
                'user_id' => $userId,
            ];
        });
    }
}