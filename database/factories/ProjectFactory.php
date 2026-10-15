<?php

namespace Database\Factories;

use App\Models\Admin;
use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Project>
 */
class ProjectFactory extends Factory
{
    protected $model = Project::class;

    public function definition(): array
    {
        $startDate = fake()->dateTimeBetween('-1 month', '+1 month');
        $endDate = (clone $startDate)->modify('+' . rand(10, 60) . ' days');

        return [
            'name' => fake()->catchPhrase() . ' Platform',
            'description' => fake()->paragraph(3),
            'status' => fake()->randomElement(['pending', 'in_progress', 'completed', 'on_hold']),
            'start_date' => $startDate,
            'end_date' => $endDate,
            'created_by' => Admin::factory(),
        ];
    }
}
