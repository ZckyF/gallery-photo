<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Folder;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Photo>
 */
class PhotoFactory extends Factory
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
            'file_name' => 'default.jpg',
            'user_id' => $this->faker->numberBetween(1, 50), 
            'folder_id' => $this->faker->numberBetween(1, 30), 
        ];
    }
}
