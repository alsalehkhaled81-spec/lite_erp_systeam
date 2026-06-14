<?php

use App\Models\Task;
use App\Models\Project;
use App\Models\Employee;
use App\Filament\Pages\TasksKanbanBoard;

describe('Admin Kanban Board', function () {

    test('super admin can access the admin kanban page', function () {
        $this->actingAs(superAdminUser())
            ->get('/admin/tasks-kanban')
            ->assertStatus(200);
    });

    test('non admin user cannot access the admin kanban page', function () {
        $role = \App\Models\Role::firstOrCreate(['name' => 'employee'], ['description' => 'Employee']);
        $user = \App\Models\User::factory()->create(['role_id' => $role->id, 'is_approved' => true]);

        $response = $this->actingAs($user)->get('/admin/tasks-kanban');

        expect($response->status())->toBeIn([301, 302, 403]);
    });

    test('kanban page renders all status columns with data-status attributes', function () {
        Task::factory()->create(['status' => 'todo']);

        $response = $this->actingAs(superAdminUser())->get('/admin/tasks-kanban');

        $response->assertStatus(200);
        $html = $response->content();

        expect($html)->toContain('data-status="todo"')
            ->and($html)->toContain('data-status="in_progress"')
            ->and($html)->toContain('data-status="review"')
            ->and($html)->toContain('data-status="done"');
    });

    test('kanban page renders draggable task cards with Alpine directives', function () {
        $task = Task::factory()->create(['status' => 'todo', 'title' => 'Admin Kanban Task']);

        $response = $this->actingAs(superAdminUser())->get('/admin/tasks-kanban');

        $response->assertStatus(200);
        $html = $response->content();

        expect($html)->toContain('kanban-card')
            ->and($html)->toContain('draggable="true"')
            ->and($html)->toContain('data-task-id="' . $task->id . '"')
            ->and($html)->toContain('x-on:dragstart')
            ->and($html)->toContain('x-on:drop.prevent')
            ->and($html)->toContain('$wire.updateTaskStatus')
            ->and($html)->toContain("setData('text/plain'");
    });

    test('admin can update task status via updateTaskStatus', function () {
        $task = Task::factory()->create(['status' => 'todo']);

        $page = new TasksKanbanBoard();
        $page->updateTaskStatus($task->id, 'done');

        expect($task->fresh()->status)->toBe('done');
    });

    test('invalid status is rejected and does not change the task', function () {
        $task = Task::factory()->create(['status' => 'todo']);

        $page = new TasksKanbanBoard();
        $page->updateTaskStatus($task->id, 'invalid_status');

        expect($task->fresh()->status)->toBe('todo');
    });

    test('admin kanban shows all tasks across employees', function () {
        $employee = Employee::factory()->create();
        $project = Project::factory()->create();

        Task::factory()->create([
            'project_id' => $project->id,
            'employee_id' => $employee->id,
            'status' => 'in_progress',
            'title' => 'Cross Employee Task',
        ]);

        $response = $this->actingAs(superAdminUser())->get('/admin/tasks-kanban');

        $response->assertStatus(200)->assertSee('Cross Employee Task', false);
    });

    test('admin kanban can filter by project', function () {
        $projectA = Project::factory()->create();
        $projectB = Project::factory()->create();

        Task::factory()->create(['project_id' => $projectA->id, 'status' => 'todo', 'title' => 'Project A Task']);
        Task::factory()->create(['project_id' => $projectB->id, 'status' => 'todo', 'title' => 'Project B Task']);

        $response = $this->actingAs(superAdminUser())
            ->get('/admin/tasks-kanban?project=' . $projectA->id);

        $response->assertStatus(200)
            ->assertSee('Project A Task', false)
            ->assertDontSee('Project B Task', false);
    });
});
