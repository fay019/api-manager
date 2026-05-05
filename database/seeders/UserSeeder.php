<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Admin Moussouni',
            'email' => 'admin@moussouni.dev',
            'password' => Hash::make('Mouloudi@1921'),
            'email_verified_at' => now(),
            'is_admin' => 1,
        ]);
    }
}
