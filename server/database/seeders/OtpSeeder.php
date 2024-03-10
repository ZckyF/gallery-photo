<?php

namespace Database\Seeders;

use Illuminate\Support\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class OtpSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\Otp::create([
            'user_id' => 3,
            'otp' => 223344,
            'expires_at' => Carbon::now()->addMinutes(30),
            'created_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);
    }
}
