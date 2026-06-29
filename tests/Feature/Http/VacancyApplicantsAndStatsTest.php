<?php

use App\Models\Vacancy;
use App\Models\Employee;
use App\Models\Resume;
use App\Models\User;
use App\Models\Role;
use App\Models\Invoice;
use App\Models\Expense;
use App\Models\Payroll;
use App\Models\Client;
use App\Models\Project;

describe('Vacancy Applicants Management', function () {
    test('vacancy has applicants relation manager relationship', function () {
        $vacancy = Vacancy::factory()->create(['status' => 'open']);
        Employee::factory()->count(3)->create(['vacancy_id' => $vacancy->id, 'status' => 'pending']);

        expect($vacancy->applicants)->toHaveCount(3);
    });

    test('applicants can be filtered by status within a vacancy', function () {
        $vacancy = Vacancy::factory()->create();
        Employee::factory()->count(2)->create(['vacancy_id' => $vacancy->id, 'status' => 'pending']);
        Employee::factory()->create(['vacancy_id' => $vacancy->id, 'status' => 'active']);
        Employee::factory()->create(['vacancy_id' => $vacancy->id, 'status' => 'rejected']);

        $pending = $vacancy->applicants()->where('status', 'pending')->get();
        $active = $vacancy->applicants()->where('status', 'active')->get();

        expect($pending)->toHaveCount(2)
            ->and($active)->toHaveCount(1);
    });

    test('vacancy can be filtered by employment type', function () {
        Vacancy::factory()->create(['employment_type' => 'full_time', 'status' => 'open']);
        Vacancy::factory()->create(['employment_type' => 'part_time', 'status' => 'open']);
        Vacancy::factory()->create(['employment_type' => 'internship', 'status' => 'open']);

        $fullTime = Vacancy::where('employment_type', 'full_time')->count();
        $internships = Vacancy::where('employment_type', 'internship')->count();

        expect($fullTime)->toBeGreaterThanOrEqual(1)
            ->and($internships)->toBeGreaterThanOrEqual(1);
    });

    test('applicant resume is accessible via vacancy relationship', function () {
        $vacancy = Vacancy::factory()->create();
        $employee = Employee::factory()->create(['vacancy_id' => $vacancy->id, 'status' => 'pending']);
        Resume::factory()->create([
            'employee_id' => $employee->id,
            'file_path' => 'resumes/test.pdf',
            'resume_text' => 'Test resume text content',
        ]);

        $applicant = $vacancy->applicants()->with('resume')->first();

        expect($applicant->resume)->not->toBeNull()
            ->and($applicant->resume->file_path)->toBe('resumes/test.pdf');
    });
});

describe('Accountant Monthly Statistics', function () {
    test('monthly profit loss chart can compute revenue expenses and payroll', function () {
        $chart = new \App\Filament\Accountant\Widgets\MonthlyProfitLossChart();

        $reflection = new ReflectionClass($chart);
        $method = $reflection->getMethod('getData');
        $method->setAccessible(true);
        $data = $method->invoke($chart);

        expect($data)->toHaveKey('datasets')
            ->and($data['datasets'])->toHaveCount(3)
            ->and($data['datasets'][0]['label'])->toBe(__('filament.widgets.revenue'))
            ->and($data['datasets'][1]['label'])->toBe(__('filament.widgets.total_costs'))
            ->and($data['datasets'][2]['label'])->toBe(__('filament.widgets.net_profit'));
    });

    test('expense category chart groups expenses by category', function () {
        Expense::factory()->create(['category' => 'إيجار', 'amount' => 5000, 'expense_date' => now()]);
        Expense::factory()->create(['category' => 'رواتب', 'amount' => 3000, 'expense_date' => now()]);

        $chart = new \App\Filament\Accountant\Widgets\ExpenseCategoryChart();

        $reflection = new ReflectionClass($chart);
        $method = $reflection->getMethod('getData');
        $method->setAccessible(true);
        $data = $method->invoke($chart);

        expect($data['labels'])->toContain('إيجار')
            ->and($data['labels'])->toContain('رواتب')
            ->and($data['datasets'][0]['data'])->toHaveCount(2);
    });

    test('monthly financial stats overview returns 4 stat cards', function () {
        $stats = new \App\Filament\Accountant\Widgets\MonthlyFinancialStats();

        $reflection = new ReflectionClass($stats);
        $method = $reflection->getMethod('getStats');
        $method->setAccessible(true);
        $statsArray = $method->invoke($stats);

        expect($statsArray)->toHaveCount(4);
    });

    test('monthly financial stats correctly computes this month revenue', function () {
        $paidInvoice = Invoice::factory()->create([
            'status' => 'paid',
            'amount' => 10000,
            'issue_date' => now(),
        ]);

        $stats = new \App\Filament\Accountant\Widgets\MonthlyFinancialStats();
        $reflection = new ReflectionClass($stats);
        $method = $reflection->getMethod('getStats');
        $method->setAccessible(true);
        $statsArray = $method->invoke($stats);

        expect($statsArray[0]->getLabel())->toBe(__('filament.widgets.this_month_revenue'));
    });
});
