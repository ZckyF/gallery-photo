<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Laravel\Sanctum\PersonalAccessToken;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        PersonalAccessToken::create([
            'tokenable_type' => 'App\\Models\\User',
            'tokenable_id' => 1,
            'name' => 'user login',
            'token' => 'bf212c7aea8cd6fb9aa5affbdc2e5a98ffe13466b24e380ad0769e9a6f26633e', // 1|TQj62MUaBupmxlDCEdS2T8FDbIdR1GEy9HwrEp63a8a21fef
            'abilities' => ["*"]
        ]);
        PersonalAccessToken::create([
            'tokenable_type' => 'App\\Models\\User',
            'tokenable_id' => 2,
            'name' => 'user login',
            'token' => 'd6d6a162c48fa8c2e6a648ce0f5421a9b1e0b0e655d0c7f9a2bd6f12306be146', // 2|3LiC4O8dp4zm74JL81vVhAL4f4oQShX9ynapU8bK6219b156
            'abilities' => ["*"]
        ]);
        PersonalAccessToken::create([
            'tokenable_type' => 'App\\Models\\User',
            'tokenable_id' => 3,
            'name' => 'user login',
            'token' => 'ec16e9def0eb5908c07fd46418f6a12100ec288583f6146529057e2b1f135a62', // 3|qniipunkUXMuN0q41eMfkeBPsWKKtmlPYhyajPEude85a21c
            'abilities' => ["*"]
        ]);
        
        $this->call(UserSeeder::class);
        // $this->call(OtpSeeder::class);
        // $this->call(FolderSeeder::class);
        // $this->call(PhotoSeeder::class);
        // $this->call(LikePhotoSeeder::class);
        // $this->call(CommentPhotoSeeder::class);
        // $this->call(SaveSeeder::class);
    }
}
