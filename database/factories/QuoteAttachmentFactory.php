<?php

namespace Database\Factories;

use App\Models\QuoteAttachment;
use App\Models\Quote;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\QuoteAttachment>
 */
class QuoteAttachmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'quote_id' => Quote::factory(),
            'name' => $this->faker->word . '.' . $this->faker->randomElement(['pdf', 'jpg', 'doc', 'xlsx']),
            'file_name' => $this->faker->word . '.' . $this->faker->randomElement(['pdf', 'jpg', 'doc', 'xlsx']),
            'file_path' => 'attachments/' . $this->faker->uuid . '.' . $this->faker->randomElement(['pdf', 'jpg', 'doc', 'xlsx']),
            'file_type' => $this->faker->randomElement(['application/pdf', 'image/jpeg', 'application/msword', 'application/vnd.ms-excel']),
            'file_size' => $this->faker->numberBetween(1000, 5000000),
            'description' => $this->faker->sentence(),
            'uploaded_by' => 1, // سيتم تعديله عند الاستخدام الفعلي
            'created_at' => now(),
            'updated_at' => now()
        ];
    }
}