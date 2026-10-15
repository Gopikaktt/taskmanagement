<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Project;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = Admin::first();
        $adminId = $admin ? $admin->id : 1;

        $projects = [
            [
                'name' => 'Cloud Infrastructure Modernization',
                'description' => 'Migrate on-premise microservices to AWS Elastic Container Service with automated CI/CD pipelines.',
                'status' => 'in_progress',
                'start_date' => now()->subDays(20),
                'end_date' => now()->addDays(40),
                'created_by' => $adminId,
            ],
            [
                'name' => 'Customer Portal Redesign V2',
                'description' => 'Complete redesign of customer dashboard with real-time analytics, dark mode, and billing management.',
                'status' => 'in_progress',
                'start_date' => now()->subDays(10),
                'end_date' => now()->addDays(25),
                'created_by' => $adminId,
            ],
            [
                'name' => 'Stripe & PayPal Payment Gateway Integration',
                'description' => 'Integrate multi-currency payment checkout with webhook verification, refund processing, and automated invoices.',
                'status' => 'pending',
                'start_date' => now()->addDays(5),
                'end_date' => now()->addDays(35),
                'created_by' => $adminId,
            ],
            [
                'name' => 'Mobile App API Services (iOS & Android)',
                'description' => 'Develop high-performance RESTful APIs with Sanctum token authentication and rate limiting.',
                'status' => 'completed',
                'start_date' => now()->subDays(60),
                'end_date' => now()->subDays(5),
                'created_by' => $adminId,
            ],
        ];

        foreach ($projects as $project) {
            Project::firstOrCreate(
                ['name' => $project['name']],
                $project
            );
        }
    }
}
