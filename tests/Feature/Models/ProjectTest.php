<?php

use App\Models\Project;
use App\Models\Client;
use App\Models\Employee;
use App\Models\Task;
use App\Models\Invoice;

describe('Project Model', function () {
    test('project has correct fillable attributes', function () {
        $project = new Project();
        expect($project->getFillable())->toContain(
            'client_id', 'name', 'description', 'budget', 'start_date', 'end_date', 'status'
        );
    });

    test('project belongs to client', function () {
        $client = Client::factory()->create();
        $project = Project::factory()->create(['client_id' => $client->id]);
        expect($project->client)->not->toBeNull()->and($project->client->id)->toBe($client->id);
    });

    test('project belongs to many employees', function () {
        $project = Project::factory()->create();
        $employees = Employee::factory()->count(3)->create();
        $project->employees()->attach($employees->pluck('id'));
        expect($project->employees)->toHaveCount(3);
    });

    test('project has many tasks', function () {
        $project = Project::factory()->create();
        Task::factory()->count(4)->create(['project_id' => $project->id]);
        expect($project->tasks)->toHaveCount(4);
    });

    test('project has many invoices', function () {
        $project = Project::factory()->create();
        Invoice::factory()->count(2)->create(['project_id' => $project->id]);
        expect($project->invoices)->toHaveCount(2);
    });
});
