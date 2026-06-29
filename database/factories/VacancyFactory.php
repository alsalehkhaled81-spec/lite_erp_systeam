<?php

namespace Database\Factories;

use App\Models\Vacancy;
use Illuminate\Database\Eloquent\Factories\Factory;

class VacancyFactory extends Factory
{
    protected $model = Vacancy::class;

    public function definition(): array
    {
        return [
            'title' => fake()->jobTitle(),
            'description' => fake()->paragraph(),
            'requirements' => 'PHP, Laravel, MySQL, JavaScript',
            'location' => fake()->city(),
            'employment_type' => fake()->randomElement(['full_time', 'part_time', 'contract', 'internship']),
            'salary_min' => fake()->randomFloat(2, 3000, 5000),
            'salary_max' => fake()->randomFloat(2, 6000, 15000),
            'department_id' => null,
            'status' => 'open',
            'positions_count' => 1,
        ];
    }
}
