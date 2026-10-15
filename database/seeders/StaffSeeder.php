<?php

namespace Database\Seeders;

use App\Models\Staff;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class StaffSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Default Active & Verified Staff
        Staff::firstOrCreate(
            ['email' => 'staff@example.com'],
            [
                'name' => 'John Doe',
                'password' => Hash::make('password123'),
                'phone' => '+1 555-0100',
                'department' => 'Software Engineering',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        // 2. Active & Verified Staff
        Staff::firstOrCreate(
            ['email' => 'sarah.smith@example.com'],
            [
                'name' => 'Sarah Smith',
                'password' => Hash::make('password123'),
                'phone' => '+1 555-0101',
                'department' => 'UI/UX Design',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        // 3. Active & Verified Staff
        Staff::firstOrCreate(
            ['email' => 'david.lee@example.com'],
            [
                'name' => 'David Lee',
                'password' => Hash::make('password123'),
                'phone' => '+1 555-0102',
                'department' => 'QA & Testing',
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        // 4. Inactive Staff (for security & eligibility test)
        Staff::firstOrCreate(
            ['email' => 'inactive.staff@example.com'],
            [
                'name' => 'Inactive Worker',
                'password' => Hash::make('password123'),
                'phone' => '+1 555-0103',
                'department' => 'Operations',
                'is_active' => false,
                'email_verified_at' => now(),
            ]
        );

        // 5. Unverified Staff (for security & eligibility test)
        Staff::firstOrCreate(
            ['email' => 'unverified.staff@example.com'],
            [
                'name' => 'Unverified Worker',
                'password' => Hash::make('password123'),
                'phone' => '+1 555-0104',
                'department' => 'Marketing',
                'is_active' => true,
                'email_verified_at' => null,
            ]
        );
    }
}
