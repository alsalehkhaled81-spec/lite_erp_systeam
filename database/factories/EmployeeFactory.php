<?php

namespace Database\Factories;

use App\Models\Employee;
use App\Models\User;
use App\Models\Department;
use Illuminate\Database\Eloquent\Factories\Factory;

class EmployeeFactory extends Factory
{
    protected $model = Employee::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'department_id' => null,
            'job_title' => fake()->jobTitle(),
            'salary' => fake()->randomFloat(2, 3000, 15000),
            'status' => 'active',
            'hire_date' => fake()->date(),
        ];
    }
}
