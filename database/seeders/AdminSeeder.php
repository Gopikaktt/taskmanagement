<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Admin::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'System Administrator',
                'password' => Hash::make('password123'),
            ]
        );

        Admin::firstOrCreate(
            ['email' => 'alex.admin@example.com'],
            [
                'name' => 'Alex Rivera',
                'password' => Hash::make('password123'),
            ]
        );
    }
}
