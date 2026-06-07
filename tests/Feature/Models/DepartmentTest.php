<?php

use App\Models\Department;
use App\Models\Employee;

describe('Department Model', function () {
    test('department has correct fillable attributes', function () {
        $dept = new Department();
        expect($dept->getFillable())->toContain('name', 'head_id');
    });

    test('department belongs to head employee', function () {
        $employee = Employee::factory()->create();
        $dept = Department::factory()->create(['head_id' => $employee->id]);
        expect($dept->head)->not->toBeNull()->and($dept->head->id)->toBe($employee->id);
    });

    test('department has many employees', function () {
        $dept = Department::factory()->create();
        Employee::factory()->count(3)->create(['department_id' => $dept->id]);
        expect($dept->employees)->toHaveCount(3);
    });
});
