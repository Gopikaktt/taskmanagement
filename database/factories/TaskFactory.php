<?php

namespace Database\Factories;

use App\Models\Admin;
use App\Models\Project;
use App\Models\Staff;
use App\Models\Task;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Task>
 */
class TaskFactory extends Factory
{
    protected $model = Task::class;

    public function definition(): array
    {
        $status = fake()->randomElement(['pending', 'in_progress', 'completed', 'cancelled']);

        return [
            'project_id' => Project::factory(),
            'staff_id' => Staff::factory(),
            'created_by' => Admin::factory(),
            'title' => fake()->sentence(5),
            'description' => fake()->paragraph(2),
            'priority' => fake()->randomElement(['low', 'medium', 'high', 'urgent']),
            'status' => $status,
            'due_date' => fake()->dateTimeBetween('now', '+30 days'),
            'completed_at' => $status === 'completed' ? now() : null,
        ];
    }

    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'completed',
            'completed_at' => now(),
        ]);
    }
}
