<?php

namespace Database\Factories;

use App\Models\Report;
use App\Models\Employee;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReportFactory extends Factory
{
    protected $model = Report::class;

    public function definition(): array
    {
        return [
            'sender_id' => Employee::factory(),
            'receiver_id' => Employee::factory(),
            'title' => fake()->sentence(),
            'content' => fake()->paragraph(),
            'feedback' => null,
            'status' => fake()->randomElement(['unread', 'read', 'replied']),
        ];
    }
}
