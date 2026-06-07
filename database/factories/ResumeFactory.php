<?php

namespace Database\Factories;

use App\Models\Resume;
use App\Models\Employee;
use Illuminate\Database\Eloquent\Factories\Factory;

class ResumeFactory extends Factory
{
    protected $model = Resume::class;

    public function definition(): array
    {
        return [
            'employee_id' => Employee::factory(),
            'file_path' => 'resumes/' . fake()->uuid . '.pdf',
            'resume_text' => fake()->paragraphs(3, true),
        ];
    }
}
