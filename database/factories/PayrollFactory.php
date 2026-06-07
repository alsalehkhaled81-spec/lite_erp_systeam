<?php

namespace Database\Factories;

use App\Models\Payroll;
use App\Models\Employee;
use Illuminate\Database\Eloquent\Factories\Factory;

class PayrollFactory extends Factory
{
    protected $model = Payroll::class;

    public function definition(): array
    {
        $basic = fake()->randomFloat(2, 3000, 15000);
        $bonuses = fake()->randomFloat(2, 0, 2000);
        $deductions = fake()->randomFloat(2, 0, 1500);

        return [
            'employee_id' => Employee::factory(),
            'month_year' => fake()->date('Y-m'),
            'basic_salary' => $basic,
            'bonuses' => $bonuses,
            'deductions' => $deductions,
            'net_salary' => Payroll::calculateNetSalary($basic, $bonuses, $deductions),
            'status' => fake()->randomElement(['paid', 'unpaid']),
        ];
    }
}
