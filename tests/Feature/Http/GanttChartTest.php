<?php

use App\Models\Role;
use App\Models\User;
use App\Models\Project;
use App\Models\Employee;

describe('PM Gantt Chart', function () {

    test('project manager can access the gantt chart page', function () {
        $this->actingAs(pmUser())
            ->get('/pm/gantt-chart')
            ->assertStatus(200);
    });

    test('non project manager is denied access to the gantt chart page', function () {
        $role = Role::factory()->create(['name' => 'employee']);
        $user = User::factory()->create(['role_id' => $role->id, 'is_approved' => true]);

        $response = $this->actingAs($user)->get('/pm/gantt-chart');

        expect($response->status())->toBeIn([301, 302, 403]);
    });

    test('gantt chart page renders the container, dynamic loader and injected tasks', function () {
        $project = Project::factory()->create();
        $employee = Employee::factory()->create();

        \App\Models\Task::factory()->create([
            'project_id' => $project->id,
            'employee_id' => $employee->id,
            'title' => 'مهمة الاختبار',
            'start_date' => '2026-01-01',
            'due_date' => '2026-01-10',
            'status' => 'in_progress',
        ]);

        $response = $this->actingAs(pmUser())->get('/pm/gantt-chart');

        $response->assertStatus(200);

        $html = $response->content();

        // Core structural elements
        expect($html)->toContain('gantt-container')
            ->and($html)->toContain('gantt-project-filter')
            ->and($html)->toContain('loadFrappeGantt');

        // Tasks are injected into the page as a JS array
        expect($html)->toContain('allTasks');

        // The frappe-gantt UMD global is `Gantt`, not `FrappeGantt`. Using the wrong
        // name leaves the chart blank with a render error.
        expect($html)->toContain('new Gantt(')
            ->and($html)->not->toContain('new FrappeGantt(')
            ->and($html)->not->toContain('typeof FrappeGantt');

        // The external CDN <script src> must NOT be rendered inline in the body,
        // because it is never re-executed under SPA navigation and left the chart blank.
        expect($html)->not->toContain('<script src="https://cdn.jsdelivr.net/npm/frappe-gantt');
    });

    test('every task is emitted with an end date strictly after its start date', function () {
        $project = Project::factory()->create();
        $employee = Employee::factory()->create();

        // Case 1: due_date before start_date (the original data bug).
        \App\Models\Task::factory()->create([
            'project_id' => $project->id,
            'employee_id' => $employee->id,
            'start_date' => '2020-05-05',
            'due_date' => '2020-05-04',
        ]);

        // Case 2: due_date equals start_date.
        \App\Models\Task::factory()->create([
            'project_id' => $project->id,
            'employee_id' => $employee->id,
            'start_date' => '2020-06-01',
            'due_date' => '2020-06-01',
        ]);

        // Case 3: missing start_date (falls back to created_at).
        \App\Models\Task::factory()->create([
            'project_id' => $project->id,
            'employee_id' => $employee->id,
            'start_date' => null,
            'due_date' => '2020-07-15',
        ]);

        $response = $this->actingAs(pmUser())->get('/pm/gantt-chart');
        $response->assertStatus(200);

        $html = $response->content();

        // The corrected end dates (start + 1 day) must be present...
        expect($html)->toContain('2020-05-06')   // case 1 corrected end
            ->and($html)->toContain('2020-06-02'); // case 2 corrected end

        // ...and the original invalid due date that would break frappe-gantt must not
        // be emitted as an end value.
        expect($html)->not->toContain('"end":"2020-05-04"');
    });

    test('empty state still renders gracefully when there are no tasks', function () {
        $response = $this->actingAs(pmUser())->get('/pm/gantt-chart');

        $response->assertStatus(200);
        expect($response->content())->toContain('gantt-container');
    });
});
