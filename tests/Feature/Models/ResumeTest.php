<?php

use App\Models\Resume;
use App\Models\Employee;

describe('Resume Model', function () {
    test('resume has correct fillable attributes', function () {
        $resume = new Resume();
        expect($resume->getFillable())->toContain('employee_id', 'file_path', 'resume_text');
    });

    test('resume belongs to employee', function () {
        $employee = Employee::factory()->create();
        $resume = Resume::factory()->create(['employee_id' => $employee->id]);
        expect($resume->employee)->not->toBeNull()->and($resume->employee->id)->toBe($employee->id);
    });
});
