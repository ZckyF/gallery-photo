<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FolderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        \App\Models\Folder::factory()->create([
            'folder_name' => 'My Collection Photos in Vacation',
            'description' => 'A collection of photos from a memorable vacation.',
            'user_id' => 2, 
        ]);
        \App\Models\Folder::factory(30)->create();
    }
}
