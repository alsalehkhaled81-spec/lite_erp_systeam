<?php

use App\Models\Leave;
use App\Models\Employee;
use Carbon\Carbon;

describe('Leave Model', function () {
    test('leave has correct fillable attributes', function () {
        $leave = new Leave();
        expect($leave->getFillable())->toContain(
            'employee_id', 'type', 'start_date', 'end_date', 'reason', 'status'
        );
    });

    test('leave casts dates correctly', function () {
        $leave = new Leave();
        $casts = $leave->getCasts();
        expect($casts)->toHaveKey('start_date')->and($casts['start_date'])->toBe('date')
            ->and($casts)->toHaveKey('end_date')->and($casts['end_date'])->toBe('date');
    });

    test('duration in days returns correct count for same day', function () {
        $leave = Leave::factory()->create([
            'start_date' => '2026-01-01',
            'end_date' => '2026-01-01',
        ]);
        expect($leave->duration_in_days)->toBe(1);
    });

    test('duration in days returns correct count for multiple days', function () {
        $leave = Leave::factory()->create([
            'start_date' => '2026-01-01',
            'end_date' => '2026-01-05',
        ]);
        expect($leave->duration_in_days)->toBe(5);
    });

    test('duration in days returns correct count for two weeks', function () {
        $leave = Leave::factory()->create([
            'start_date' => '2026-01-01',
            'end_date' => '2026-01-14',
        ]);
        expect($leave->duration_in_days)->toBe(14);
    });

    test('leave belongs to employee', function () {
        $employee = Employee::factory()->create();
        $leave = Leave::factory()->create(['employee_id' => $employee->id]);
        expect($leave->employee)->not->toBeNull()->and($leave->employee->id)->toBe($employee->id);
    });
});
