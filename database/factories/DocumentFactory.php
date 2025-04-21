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
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Document::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Create a parent request by default
        $request = Request::factory()->create();
        $user = User::factory()->create();

        return [
            'name' => $this->faker->word . '.pdf',
            'file_path' => 'documents/' . $this->faker->word . '.pdf',
            'file_type' => 'application/pdf',
            'size' => $this->faker->numberBetween(100, 1024 * 1024 * 5),
            'documentable_id' => $request->id,
            'documentable_type' => Request::class,
            'uploaded_by' => $user->id,
            'visibility' => 'private',
            'notes' => $this->faker->sentence(),
        ];
    }
}