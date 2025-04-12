<?php

namespace Database\Factories;

use App\Models\Document;
use App\Models\User;
use App\Models\Request;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Document>
 */
class DocumentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $documentableTypes = [
            Request::class,
        ];
        $documentableType = $this->faker->randomElement($documentableTypes);
        $user = User::factory()->create();

        return [
            'name' => $this->faker->words(3, true) . '.' . $this->faker->randomElement(['pdf', 'doc', 'jpg']),
            'file_path' => 'documents/' . $this->faker->uuid . '.' . $this->faker->randomElement(['pdf', 'doc', 'jpg']),
            'file_type' => $this->faker->randomElement(['application/pdf', 'application/msword', 'image/jpeg']),
            'size' => $this->faker->numberBetween(1000, 10000000),
            'documentable_id' => 1, // سيتم تعيينه لاحقًا
            'documentable_type' => $documentableType,
            'user_id' => $user->id, // إضافة user_id الإلزامي
            'uploaded_by' => $user->id, // استخدام نفس المستخدم
            'visibility' => $this->faker->randomElement(['public', 'agency', 'private']),
            'notes' => $this->faker->sentence()
        ];
    }

    /**
     * تعيين النوع والمعرّف للعنصر الموثق
     */
    public function forDocumentable($documentable)
    {
        return $this->state(function (array $attributes) use ($documentable) {
            return [
                'documentable_id' => $documentable->id,
                'documentable_type' => get_class($documentable),
            ];
        });
    }
}