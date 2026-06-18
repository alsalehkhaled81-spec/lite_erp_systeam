<?php

use App\Models\Attendance;
use App\Models\Employee;
use App\Models\User;

describe('Attendance', function () {

    test('HR manager can access the attendance list page', function () {
        $this->actingAs(hrUser())
            ->get('/hr/attendances')
            ->assertStatus(200);
    });

    test('create form uses time pickers and shows hours worked field', function () {
        $response = $this->actingAs(hrUser())->get('/hr/attendances/create');

        $response->assertStatus(200);
        $html = $response->content();
        expect($html)
            ->toContain('check_in')
            ->toContain('check_out')
            ->toContain('hours_worked')
            ->toContain(__('filament.fields.hours_worked_auto'));
    });

    test('calculateHoursFromTimes computes hours between two times', function () {
        expect(Attendance::calculateHoursFromTimes('09:00', '17:00'))->toBe(8.0)
            ->and(Attendance::calculateHoursFromTimes('08:30', '12:00'))->toBe(3.5)
            ->and(Attendance::calculateHoursFromTimes('09:00', '09:00'))->toBe(0.0)
            ->and(Attendance::calculateHoursFromTimes(null, '17:00'))->toBe(0.0);
    });

    test('combineDateTime merges a date and a time into a single datetime', function () {
        $dt = Attendance::combineDateTime('2026-06-17', '14:30');

        expect($dt)->not->toBeNull()
            ->and($dt->toDateString())->toBe('2026-06-17')
            ->and($dt->format('H:i'))->toBe('14:30')
            ->and(Attendance::combineDateTime(null, '14:30'))->toBeNull();
    });

    test('hours worked is auto-calculated from check-in and check-out', function () {
        $employeeUser = User::factory()->create(['name' => 'Test Employee Hours']);
        $employee = Employee::factory()->create(['user_id' => $employeeUser->id]);

        $attendance = Attendance::create([
            'employee_id' => $employee->id,
            'date' => '2026-06-17',
            'check_in' => Attendance::combineDateTime('2026-06-17', '09:00'),
            'check_out' => Attendance::combineDateTime('2026-06-17', '17:30'),
            'status' => 'present',
        ]);

        $hours = Attendance::calculateHoursWorked($attendance->check_in, $attendance->check_out);

        expect($hours)->toBe(8.5);
    });

    test('check-in and check-out always fall on the same day', function () {
        $date = '2026-06-17';
        $checkIn = Attendance::combineDateTime($date, '08:00');
        $checkOut = Attendance::combineDateTime($date, '16:00');

        expect($checkIn->toDateString())->toBe($date)
            ->and($checkOut->toDateString())->toBe($date);
    });
});
