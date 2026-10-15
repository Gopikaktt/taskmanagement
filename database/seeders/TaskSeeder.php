<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Project;
use App\Models\Staff;
use App\Models\Task;
use Illuminate\Database\Seeder;

class TaskSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = Admin::first();
        $adminId = $admin ? $admin->id : 1;

        $project1 = Project::where('name', 'Cloud Infrastructure Modernization')->first();
        $project2 = Project::where('name', 'Customer Portal Redesign V2')->first();
        $project3 = Project::where('name', 'Stripe & PayPal Payment Gateway Integration')->first();
        $project4 = Project::where('name', 'Mobile App API Services (iOS & Android)')->first();

        $staffJohn = Staff::where('email', 'staff@example.com')->first();
        $staffSarah = Staff::where('email', 'sarah.smith@example.com')->first();
        $staffDavid = Staff::where('email', 'david.lee@example.com')->first();

        $tasks = [
            [
                'project_id' => $project1?->id ?? 1,
                'staff_id' => $staffJohn?->id,
                'created_by' => $adminId,
                'title' => 'Configure Terraform for AWS ECS Cluster',
                'description' => 'Write clean infrastructure-as-code modules for VPC, subnets, application load balancer, and ECS Fargate cluster.',
                'priority' => 'high',
                'status' => 'in_progress',
                'due_date' => now()->addDays(7),
                'completed_at' => null,
            ],
            [
                'project_id' => $project1?->id ?? 1,
                'staff_id' => $staffJohn?->id,
                'created_by' => $adminId,
                'title' => 'Set up GitHub Actions Docker Build & Push Pipeline',
                'description' => 'Automate multi-stage Docker builds and push images to AWS ECR on merge to main branch.',
                'priority' => 'medium',
                'status' => 'pending',
                'due_date' => now()->addDays(14),
                'completed_at' => null,
            ],
            [
                'project_id' => $project2?->id ?? 2,
                'staff_id' => $staffSarah?->id,
                'created_by' => $adminId,
                'title' => 'Design High-Fidelity Figma Mockups for Portal Dashboard',
                'description' => 'Create responsive desktop and mobile designs with dark mode token library and component variants.',
                'priority' => 'urgent',
                'status' => 'completed',
                'due_date' => now()->subDays(2),
                'completed_at' => now()->subDays(2),
            ],
            [
                'project_id' => $project2?->id ?? 2,
                'staff_id' => $staffSarah?->id,
                'created_by' => $adminId,
                'title' => 'Implement Reusable Chart.js Analytics Widgets',
                'description' => 'Build reactive Blade / Vanilla JS chart widgets for revenue stats and task distribution metrics.',
                'priority' => 'medium',
                'status' => 'in_progress',
                'due_date' => now()->addDays(10),
                'completed_at' => null,
            ],
            [
                'project_id' => $project3?->id ?? 3,
                'staff_id' => $staffDavid?->id,
                'created_by' => $adminId,
                'title' => 'Write Automated Unit & Integration Tests for Webhook Handlers',
                'description' => 'Ensure Stripe payment intent succeeded and failed webhooks are idempotently handled and logged.',
                'priority' => 'high',
                'status' => 'pending',
                'due_date' => now()->addDays(18),
                'completed_at' => null,
            ],
            [
                'project_id' => $project4?->id ?? 4,
                'staff_id' => $staffDavid?->id,
                'created_by' => $adminId,
                'title' => 'API Endpoint Load Testing with Apache JMeter',
                'description' => 'Benchmark task listing and status update endpoints at 500 concurrent requests/second.',
                'priority' => 'low',
                'status' => 'completed',
                'due_date' => now()->subDays(10),
                'completed_at' => now()->subDays(8),
            ],
        ];

        foreach ($tasks as $task) {
            Task::firstOrCreate(
                ['title' => $task['title'], 'project_id' => $task['project_id']],
                $task
            );
        }
    }
}
