<?php

use App\Models\Task;
use App\Models\Project;
use App\Models\Employee;

describe('Task Model', function () {
    test('task has correct fillable attributes', function () {
        $task = new Task();
        expect($task->getFillable())->toContain(
            'project_id', 'employee_id', 'title', 'description', 'due_date', 'status', 'priority'
        );
    });

    test('task belongs to project', function () {
        $project = Project::factory()->create();
        $task = Task::factory()->create(['project_id' => $project->id]);
        expect($task->project)->not->toBeNull()->and($task->project->id)->toBe($project->id);
    });

    test('task belongs to employee', function () {
        $employee = Employee::factory()->create();
        $task = Task::factory()->create(['employee_id' => $employee->id]);
        expect($task->employee)->not->toBeNull()->and($task->employee->id)->toBe($employee->id);
    });
});
