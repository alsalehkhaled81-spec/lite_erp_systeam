<?php

use App\Models\Vacancy;
use App\Models\Employee;
use App\Models\User;
use App\Models\Department;

describe('Vacancy Model', function () {
    test('vacancy has correct fillable attributes', function () {
        $vacancy = new Vacancy();
        expect($vacancy->getFillable())->toContain(
            'title', 'description', 'requirements', 'location', 'employment_type',
            'salary_min', 'salary_max', 'department_id', 'status', 'positions_count', 'created_by'
        );
    });

    test('vacancy belongs to department', function () {
        $department = Department::factory()->create();
        $vacancy = Vacancy::factory()->create(['department_id' => $department->id]);

        expect($vacancy->department)->not->toBeNull()
            ->and($vacancy->department->id)->toBe($department->id);
    });

    test('vacancy belongs to creator', function () {
        $user = User::factory()->create();
        $vacancy = Vacancy::factory()->create(['created_by' => $user->id]);

        expect($vacancy->creator)->not->toBeNull()
            ->and($vacancy->creator->id)->toBe($user->id);
    });

    test('vacancy has many applicants', function () {
        $vacancy = Vacancy::factory()->create();
        Employee::factory()->count(3)->create(['vacancy_id' => $vacancy->id, 'status' => 'pending']);

        expect($vacancy->applicants)->toHaveCount(3)
            ->and($vacancy->applicants_count)->toBe(3);
    });

    test('vacancy counts only pending applicants via scope', function () {
        $vacancy = Vacancy::factory()->create();
        Employee::factory()->count(2)->create(['vacancy_id' => $vacancy->id, 'status' => 'pending']);
        Employee::factory()->create(['vacancy_id' => $vacancy->id, 'status' => 'active']);

        expect($vacancy->pendingApplicants()->count())->toBe(2)
            ->and($vacancy->applicants()->count())->toBe(3);
    });

    test('vacancy defaults to open status', function () {
        $vacancy = Vacancy::factory()->create();
        expect($vacancy->status)->toBe('open');
    });
});

describe('Employee Vacancy Relationship', function () {
    test('employee belongs to vacancy', function () {
        $vacancy = Vacancy::factory()->create(['title' => 'Backend Developer']);
        $employee = Employee::factory()->create(['vacancy_id' => $vacancy->id]);

        expect($employee->vacancy)->not->toBeNull()
            ->and($employee->vacancy->id)->toBe($vacancy->id)
            ->and($employee->vacancy->title)->toBe('Backend Developer');
    });

    test('employee vacancy is nullable', function () {
        $employee = Employee::factory()->create(['vacancy_id' => null]);
        expect($employee->vacancy)->toBeNull();
    });
});
