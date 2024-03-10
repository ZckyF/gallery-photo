<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PhotoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {


        \App\Models\Photo::factory()->create([
            'title' => 'Sunset on the Beach',
            'description' => 'A beautiful sunset view with waves crashing on the shore.',
            'file_name' => 'default.jpg', 
            'user_id' => 2, 
            'folder_id' => 1, 
        ]);
        \App\Models\Photo::factory(40)->create();
    }
}
