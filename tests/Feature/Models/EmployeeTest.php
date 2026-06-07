<?php

use App\Models\Employee;
use App\Models\User;
use App\Models\Department;
use App\Models\Resume;
use App\Models\Skill;
use App\Models\Project;
use App\Models\Task;
use App\Models\Leave;
use App\Models\Payroll;
use App\Models\Report;

describe('Employee Model', function () {
    test('employee has correct fillable attributes', function () {
        $employee = new Employee();
        expect($employee->getFillable())->toContain(
            'user_id', 'department_id', 'job_title', 'salary', 'status', 'hire_date'
        );
    });

    test('employee casts hire_date as date', function () {
        $employee = new Employee();
        $casts = $employee->getCasts();
        expect($casts)->toHaveKey('hire_date')->and($casts['hire_date'])->toBe('date');
    });

    test('employee belongs to user', function () {
        $user = User::factory()->create();
        $employee = Employee::factory()->create(['user_id' => $user->id]);
        expect($employee->user)->not->toBeNull()->and($employee->user->id)->toBe($user->id);
    });

    test('employee belongs to department', function () {
        $dept = Department::factory()->create();
        $employee = Employee::factory()->create(['department_id' => $dept->id]);
        expect($employee->department)->not->toBeNull()->and($employee->department->id)->toBe($dept->id);
    });

    test('employee has one resume', function () {
        $employee = Employee::factory()->create();
        $resume = Resume::factory()->create(['employee_id' => $employee->id]);
        expect($employee->resume)->not->toBeNull()->and($employee->resume->id)->toBe($resume->id);
    });

    test('employee belongs to many skills', function () {
        $employee = Employee::factory()->create();
        $skills = Skill::factory()->count(3)->create();
        $employee->skills()->attach($skills->pluck('id'));
        expect($employee->skills)->toHaveCount(3);
    });

    test('employee belongs to many projects', function () {
        $employee = Employee::factory()->create();
        $projects = Project::factory()->count(2)->create();
        $employee->projects()->attach($projects->pluck('id'));
        expect($employee->projects)->toHaveCount(2);
    });

    test('employee has many tasks', function () {
        $employee = Employee::factory()->create();
        Task::factory()->count(3)->create(['employee_id' => $employee->id]);
        expect($employee->tasks)->toHaveCount(3);
    });

    test('employee has many leaves', function () {
        $employee = Employee::factory()->create();
        Leave::factory()->count(2)->create(['employee_id' => $employee->id]);
        expect($employee->leaves)->toHaveCount(2);
    });

    test('employee has many payrolls', function () {
        $employee = Employee::factory()->create();
        Payroll::factory()->count(2)->create(['employee_id' => $employee->id]);
        expect($employee->payrolls)->toHaveCount(2);
    });

    test('employee has many sent reports', function () {
        $employee = Employee::factory()->create();
        Report::factory()->count(2)->create(['sender_id' => $employee->id]);
        expect($employee->sentReports)->toHaveCount(2);
    });

    test('employee has many received reports', function () {
        $employee = Employee::factory()->create();
        Report::factory()->count(2)->create(['receiver_id' => $employee->id]);
        expect($employee->receivedReports)->toHaveCount(2);
    });

    test('employee is head of department', function () {
        $employee = Employee::factory()->create();
        $dept = Department::factory()->create(['head_id' => $employee->id]);
        expect($employee->headOfDepartment)->not->toBeNull()->and($employee->headOfDepartment->id)->toBe($dept->id);
    });
});
