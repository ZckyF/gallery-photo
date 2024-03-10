<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ReportPhoto>
 */
class ReportPhotoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence,
            'description' => $this->faker->paragraph,
            'photo_id' => $this->faker->numberBetween(1,40),
            'user_id' => $this->faker->numberBetween(1,50),
            'created_at' => now(),
        ];
    }
}
