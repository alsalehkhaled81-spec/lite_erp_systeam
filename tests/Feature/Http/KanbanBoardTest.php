<?php

use App\Models\Role;
use App\Models\User;
use App\Models\Project;
use App\Models\Employee;
use App\Models\Task;
use App\Filament\Pm\Pages\TasksKanbanBoard;

describe('PM Kanban Board', function () {

    test('kanban board page renders with columns and draggable cards', function () {
        $project = Project::factory()->create();
        $employee = Employee::factory()->create();
        $task = Task::factory()->create([
            'project_id' => $project->id,
            'employee_id' => $employee->id,
            'status' => 'todo',
            'title' => 'مهمة كانبان',
        ]);

        $response = $this->actingAs(pmUser())->get('/pm/tasks-kanban');

        $response->assertStatus(200);

        $html = $response->content();

        // Columns exist with data-status (the drop target identifier)
        expect($html)->toContain('kanban-column')
            ->and($html)->toContain('data-status="todo"')
            ->and($html)->toContain('data-status="in_progress"')
            ->and($html)->toContain('data-status="review"')
            ->and($html)->toContain('data-status="done"');

        // Card is draggable
        expect($html)->toContain('draggable="true"')
            ->and($html)->toContain('data-task-id="' . $task->id . '"')
            ->and($html)->toContain('kanban-card');

        // Drag handlers are wired via Alpine directives (not addEventListener)
        expect($html)->toContain('x-on:dragstart')
            ->and($html)->toContain('x-on:drop.prevent');

        // The drop handler calls $wire.updateTaskStatus (Alpine expression scope)
        expect($html)->toContain('$wire.updateTaskStatus');

        // dragstart sets dataTransfer (required by Firefox to initiate drag)
        expect($html)->toContain("setData('text/plain'");
    });

    test('updateTaskStatus moves a task from todo to in_progress', function () {
        $project = Project::factory()->create();
        $employee = Employee::factory()->create();
        $task = Task::factory()->create([
            'project_id' => $project->id,
            'employee_id' => $employee->id,
            'status' => 'todo',
        ]);

        $page = new TasksKanbanBoard();
        $page->updateTaskStatus($task->id, 'in_progress');

        expect($task->fresh()->status)->toBe('in_progress');
    });

    test('updateTaskStatus moves a task through all statuses', function () {
        $project = Project::factory()->create();
        $employee = Employee::factory()->create();
        $task = Task::factory()->create([
            'project_id' => $project->id,
            'employee_id' => $employee->id,
            'status' => 'todo',
        ]);

        $page = new TasksKanbanBoard();

        $page->updateTaskStatus($task->id, 'in_progress');
        expect($task->fresh()->status)->toBe('in_progress');

        $page->updateTaskStatus($task->id, 'review');
        expect($task->fresh()->status)->toBe('review');

        $page->updateTaskStatus($task->id, 'done');
        expect($task->fresh()->status)->toBe('done');

        $page->updateTaskStatus($task->id, 'todo');
        expect($task->fresh()->status)->toBe('todo');
    });

    test('updateTaskStatus rejects an invalid status', function () {
        $project = Project::factory()->create();
        $employee = Employee::factory()->create();
        $task = Task::factory()->create([
            'project_id' => $project->id,
            'employee_id' => $employee->id,
            'status' => 'todo',
        ]);

        $page = new TasksKanbanBoard();
        $page->updateTaskStatus($task->id, 'hacked_status');

        expect($task->fresh()->status)->toBe('todo');
    });

    test('updateTaskStatus ignores a non-existent task id', function () {
        $page = new TasksKanbanBoard();

        // Should not throw
        $page->updateTaskStatus(999999, 'done');

        expect(Task::find(999999))->toBeNull();
    });

    test('updateTaskStatus inserts task at the correct position within a column', function () {
        $project = Project::factory()->create();
        $employee = Employee::factory()->create();

        $taskA = Task::factory()->create(['project_id' => $project->id, 'employee_id' => $employee->id, 'status' => 'todo', 'sort_order' => 0]);
        $taskB = Task::factory()->create(['project_id' => $project->id, 'employee_id' => $employee->id, 'status' => 'todo', 'sort_order' => 1]);
        $taskC = Task::factory()->create(['project_id' => $project->id, 'employee_id' => $employee->id, 'status' => 'todo', 'sort_order' => 2]);

        $page = new TasksKanbanBoard();

        // Move taskC (index 2) to position 0
        $page->updateTaskStatus($taskC->id, 'todo', 0);

        $ordered = Task::where('status', 'todo')->orderBy('sort_order')->pluck('id')->toArray();
        expect($ordered)->toBe([$taskC->id, $taskA->id, $taskB->id]);
    });

    test('updateTaskStatus inserts task between two existing tasks', function () {
        $project = Project::factory()->create();
        $employee = Employee::factory()->create();

        $taskA = Task::factory()->create(['project_id' => $project->id, 'employee_id' => $employee->id, 'status' => 'done', 'sort_order' => 0]);
        $taskB = Task::factory()->create(['project_id' => $project->id, 'employee_id' => $employee->id, 'status' => 'done', 'sort_order' => 1]);
        $taskC = Task::factory()->create(['project_id' => $project->id, 'employee_id' => $employee->id, 'status' => 'todo', 'sort_order' => 0]);

        $page = new TasksKanbanBoard();

        // Move taskC from todo to done at position 1 (between A and B)
        $page->updateTaskStatus($taskC->id, 'done', 1);

        $ordered = Task::where('status', 'done')->orderBy('sort_order')->pluck('id')->toArray();
        expect($ordered)->toBe([$taskA->id, $taskC->id, $taskB->id]);
    });

    test('tasks are displayed sorted by sort_order', function () {
        $project = Project::factory()->create();
        $employee = Employee::factory()->create();

        Task::factory()->create(['project_id' => $project->id, 'employee_id' => $employee->id, 'status' => 'todo', 'sort_order' => 5, 'title' => 'Fifth']);
        Task::factory()->create(['project_id' => $project->id, 'employee_id' => $employee->id, 'status' => 'todo', 'sort_order' => 1, 'title' => 'First']);
        Task::factory()->create(['project_id' => $project->id, 'employee_id' => $employee->id, 'status' => 'todo', 'sort_order' => 3, 'title' => 'Third']);

        $response = $this->actingAs(pmUser())->get('/pm/tasks-kanban');

        $response->assertStatus(200);
        $html = $response->content();

        $posFirst = strpos($html, 'First');
        $posThird = strpos($html, 'Third');
        $posFifth = strpos($html, 'Fifth');

        expect($posFirst)->toBeLessThan($posThird)
            ->and($posThird)->toBeLessThan($posFifth);
    });
});
