<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User; // 1. Pastikan ini User (singular)

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 2. Gunakan User::create
        User::create([
            'name'     => 'admin',
            'email'    => 'admin@gmail.com',
            'password' => Hash::make('admin123'),
        ]);
    }
}