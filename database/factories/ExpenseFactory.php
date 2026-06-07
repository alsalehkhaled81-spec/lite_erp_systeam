<?php

namespace Database\Factories;

use App\Models\Expense;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ExpenseFactory extends Factory
{
    protected $model = Expense::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'title' => fake()->sentence(3),
            'category' => fake()->randomElement(['رواتب', 'تشغيل', 'أدوات', 'تسويق', 'أخرى']),
            'amount' => fake()->randomFloat(2, 100, 10000),
            'expense_date' => fake()->date(),
            'receipt_url' => null,
        ];
    }
}
