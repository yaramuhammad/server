<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::factory()->superAdmin()->create([
            'name' => 'Super Admin',
            'email' => 'superadmin@edrak.com',
            'preferred_locale' => 'en',
        ]);

        User::factory()->create([
            'name' => 'Dr. Sarah Ahmed',
            'email' => 'sarah@edrak.com',
            'preferred_locale' => 'ar',
        ]);

        User::factory()->create([
            'name' => 'Dr. Omar Hassan',
            'email' => 'omar@edrak.com',
            'preferred_locale' => 'en',
        ]);
    }
}
