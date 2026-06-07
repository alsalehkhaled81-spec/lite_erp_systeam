<?php

namespace Database\Factories;

use App\Models\Leave;
use App\Models\Employee;
use Illuminate\Database\Eloquent\Factories\Factory;

class LeaveFactory extends Factory
{
    protected $model = Leave::class;

    public function definition(): array
    {
        $startDate = fake()->dateTimeBetween('now', '+1 month');
        $endDate = (clone $startDate)->modify('+' . fake()->numberBetween(1, 14) . ' days');

        return [
            'employee_id' => Employee::factory(),
            'type' => fake()->randomElement(['Sick', 'Annual', 'Emergency']),
            'start_date' => $startDate->format('Y-m-d'),
            'end_date' => $endDate->format('Y-m-d'),
            'reason' => fake()->sentence(),
            'status' => fake()->randomElement(['pending', 'approved_by_head', 'approved_by_hr', 'rejected']),
        ];
    }
}
