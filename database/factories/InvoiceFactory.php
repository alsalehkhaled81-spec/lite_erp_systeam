<?php

namespace Database\Factories;

use App\Models\Invoice;
use App\Models\Client;
use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;

class InvoiceFactory extends Factory
{
    protected $model = Invoice::class;

    public function definition(): array
    {
        return [
            'client_id' => Client::factory(),
            'project_id' => Project::factory(),
            'invoice_number' => 'INV-' . fake()->unique()->numberBetween(1000, 9999),
            'amount' => fake()->randomFloat(2, 1000, 50000),
            'issue_date' => fake()->date(),
            'due_date' => fake()->dateTimeBetween('+1 week', '+2 months')->format('Y-m-d'),
            'status' => fake()->randomElement(['unpaid', 'paid', 'overdue']),
        ];
    }
}
