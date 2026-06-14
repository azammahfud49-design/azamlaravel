<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::factory()->create([
            'name'              => 'Test User',
            'email'             => 'test@example.com',
            'email_verified_at' => now(),
        ]);

        $this->call(MahasiswaSeeder::class);
    }
}
