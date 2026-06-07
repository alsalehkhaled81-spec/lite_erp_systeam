<?php

namespace Database\Factories;

use App\Models\Project;
use App\Models\Client;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProjectFactory extends Factory
{
    protected $model = Project::class;

    public function definition(): array
    {
        return [
            'client_id' => Client::factory(),
            'name' => fake()->catchPhrase(),
            'description' => fake()->paragraph(),
            'budget' => fake()->randomFloat(2, 5000, 100000),
            'start_date' => fake()->date(),
            'end_date' => fake()->dateTimeBetween('+1 month', '+6 months')->format('Y-m-d'),
            'status' => fake()->randomElement(['pending', 'in_progress', 'completed', 'canceled']),
        ];
    }
}
