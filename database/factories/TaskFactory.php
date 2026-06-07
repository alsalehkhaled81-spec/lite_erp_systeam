<?php

namespace Database\Factories;

use App\Models\Task;
use App\Models\Project;
use App\Models\Employee;
use Illuminate\Database\Eloquent\Factories\Factory;

class TaskFactory extends Factory
{
    protected $model = Task::class;

    public function definition(): array
    {
        return [
            'project_id' => Project::factory(),
            'employee_id' => Employee::factory(),
            'title' => fake()->sentence(4),
            'description' => fake()->paragraph(),
            'due_date' => fake()->dateTimeBetween('now', '+1 month')->format('Y-m-d'),
            'status' => fake()->randomElement(['todo', 'in_progress', 'review', 'done']),
            'priority' => fake()->randomElement(['low', 'medium', 'high']),
        ];
    }
}
