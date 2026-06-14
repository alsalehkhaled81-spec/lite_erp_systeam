<?php

use App\Models\Employee;
use App\Models\Leave;
use App\Models\User;

describe('Team Calendar', function () {

    test('HR manager can access the team calendar page', function () {
        $this->actingAs(hrUser())
            ->get('/hr/team-calendar')
            ->assertStatus(200);
    });

    test('page contains the calendar container div', function () {
        $response = $this->actingAs(hrUser())->get('/hr/team-calendar');

        $response->assertStatus(200);
        expect($response->content())->toContain('id="team-calendar"');
    });

    test('page contains the dynamic loader function', function () {
        $response = $this->actingAs(hrUser())->get('/hr/team-calendar');

        $response->assertStatus(200);
        expect($response->content())->toContain('loadFullCalendar');
    });

    test('page does not contain inline script src CDN tags', function () {
        $response = $this->actingAs(hrUser())->get('/hr/team-calendar');

        $response->assertStatus(200);
        expect($response->content())
            ->not->toContain('<script src="https://cdn.jsdelivr.net/npm/fullcalendar');
    });

    test('events JSON is injected properly with approved leave data', function () {
        $employeeUser = User::factory()->create(['name' => 'Ahmed Calendar Tester']);
        $employee = Employee::factory()->create(['user_id' => $employeeUser->id]);

        Leave::factory()->create([
            'employee_id' => $employee->id,
            'status' => 'approved_by_hr',
            'type' => 'Annual',
        ]);

        $response = $this->actingAs(hrUser())->get('/hr/team-calendar');

        $response->assertStatus(200);
        expect($response->content())->toContain('calendarEvents')
            ->and($response->content())->toContain('Ahmed Calendar Tester');
    });

    test('page locale is set dynamically from app locale', function () {
        $response = $this->actingAs(hrUser())->get('/hr/team-calendar');

        $response->assertStatus(200);
        expect($response->content())->toContain("locale:");
    });
});
