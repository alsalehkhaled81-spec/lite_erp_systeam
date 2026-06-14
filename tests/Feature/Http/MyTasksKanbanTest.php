<?php

use App\Models\Role;
use App\Models\User;
use App\Models\Project;
use App\Models\Employee;
use App\Models\Task;
use App\Filament\Employee\Pages\MyTasksKanban;

describe('Employee Kanban Board', function () {

    function employeeUserWithEmployee(): array
    {
        $role = Role::firstOrCreate(['name' => 'employee'], ['description' => 'Employee']);
        $user = User::factory()->create([
            'role_id' => $role->id,
            'is_approved' => true,
        ]);
        $employee = Employee::factory()->create(['user_id' => $user->id]);

        return [$user, $employee];
    }

    test('employee kanban page renders with columns and draggable cards', function () {
        [$user, $employee] = employeeUserWithEmployee();
        $project = Project::factory()->create();
        $task = Task::factory()->create([
            'project_id' => $project->id,
            'employee_id' => $employee->id,
            'status' => 'todo',
            'title' => 'مهمتي',
        ]);

        $response = $this->actingAs($user)->get('/employee/my-tasks-kanban');

        $response->assertStatus(200);

        $html = $response->content();

        expect($html)->toContain('kanban-column')
            ->and($html)->toContain('data-status="todo"')
            ->and($html)->toContain('data-status="in_progress"')
            ->and($html)->toContain('data-status="review"')
            ->and($html)->toContain('data-status="done"')
            ->and($html)->toContain('draggable="true"')
            ->and($html)->toContain('data-task-id="' . $task->id . '"')
            ->and($html)->toContain('kanban-card')
            ->and($html)->toContain('x-on:dragstart')
            ->and($html)->toContain('x-on:drop.prevent')
            ->and($html)->toContain('$wire.updateTaskStatus')
            ->and($html)->toContain("setData('text/plain'");
    });

    test('updateTaskStatus moves own task from todo to done', function () {
        [$user, $employee] = employeeUserWithEmployee();
        $project = Project::factory()->create();
        $task = Task::factory()->create([
            'project_id' => $project->id,
            'employee_id' => $employee->id,
            'status' => 'todo',
        ]);

        $this->actingAs($user);
        $page = new MyTasksKanban();
        $page->updateTaskStatus($task->id, 'done');

        expect($task->fresh()->status)->toBe('done');
    });

    test('updateTaskStatus moves task through all statuses', function () {
        [$user, $employee] = employeeUserWithEmployee();
        $project = Project::factory()->create();
        $task = Task::factory()->create([
            'project_id' => $project->id,
            'employee_id' => $employee->id,
            'status' => 'todo',
        ]);

        $this->actingAs($user);
        $page = new MyTasksKanban();

        $page->updateTaskStatus($task->id, 'in_progress');
        expect($task->fresh()->status)->toBe('in_progress');

        $page->updateTaskStatus($task->id, 'review');
        expect($task->fresh()->status)->toBe('review');

        $page->updateTaskStatus($task->id, 'done');
        expect($task->fresh()->status)->toBe('done');

        $page->updateTaskStatus($task->id, 'todo');
        expect($task->fresh()->status)->toBe('todo');
    });

    test('employee cannot change status of another employee task', function () {
        [$user, $myEmployee] = employeeUserWithEmployee();

        $otherRole = Role::firstOrCreate(['name' => 'employee'], ['description' => 'Employee']);
        $otherUser = User::factory()->create(['role_id' => $otherRole->id, 'is_approved' => true]);
        $otherEmployee = Employee::factory()->create(['user_id' => $otherUser->id]);
        $project = Project::factory()->create();
        $otherTask = Task::factory()->create([
            'project_id' => $project->id,
            'employee_id' => $otherEmployee->id,
            'status' => 'todo',
        ]);

        $this->actingAs($user);
        $page = new MyTasksKanban();
        $page->updateTaskStatus($otherTask->id, 'done');

        // Status should remain unchanged because the task doesn't belong to this employee
        expect($otherTask->fresh()->status)->toBe('todo');
    });

    test('updateTaskStatus rejects an invalid status', function () {
        [$user, $employee] = employeeUserWithEmployee();
        $project = Project::factory()->create();
        $task = Task::factory()->create([
            'project_id' => $project->id,
            'employee_id' => $employee->id,
            'status' => 'todo',
        ]);

        $this->actingAs($user);
        $page = new MyTasksKanban();
        $page->updateTaskStatus($task->id, 'hacked_status');

        expect($task->fresh()->status)->toBe('todo');
    });

    test('updateTaskStatus ignores a non-existent task id', function () {
        [$user, $employee] = employeeUserWithEmployee();
        $this->actingAs($user);

        $page = new MyTasksKanban();
        $page->updateTaskStatus(999999, 'done');

        expect(Task::find(999999))->toBeNull();
    });

    test('employee kanban only shows the authenticated employee tasks', function () {
        [$user, $myEmployee] = employeeUserWithEmployee();

        $otherRole = Role::firstOrCreate(['name' => 'employee'], ['description' => 'Employee']);
        $otherUser = User::factory()->create(['role_id' => $otherRole->id, 'is_approved' => true]);
        $otherEmployee = Employee::factory()->create(['user_id' => $otherUser->id]);

        $project = Project::factory()->create();
        Task::factory()->create([
            'project_id' => $project->id,
            'employee_id' => $myEmployee->id,
            'title' => 'مهمتي الخاصة',
        ]);
        Task::factory()->create([
            'project_id' => $project->id,
            'employee_id' => $otherEmployee->id,
            'title' => 'مهمة زميلي',
        ]);

        $response = $this->actingAs($user)->get('/employee/my-tasks-kanban');

        $response->assertStatus(200);
        $response->assertSee('مهمتي الخاصة');
        $response->assertDontSee('مهمة زميلي');
    });
});
