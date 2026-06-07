<?php

use App\Models\Employee;
use App\Models\Project;
use App\Models\Task;
use App\Models\Client;

describe('Landing Page', function () {
    test('landing page returns 200', function () {
        $this->get('/')->assertStatus(200);
    });

    test('landing page shows correct active employee count', function () {
        Employee::factory()->count(3)->create(['status' => 'active']);
        Employee::factory()->count(2)->create(['status' => 'terminated']);

        $response = $this->get('/');
        $response->assertStatus(200);
    });

    test('landing page shows correct project count', function () {
        $client = Client::factory()->create();
        Project::factory()->count(5)->create(['client_id' => $client->id]);

        $response = $this->get('/');
        $response->assertStatus(200);
    });

    test('landing page shows completed tasks count', function () {
        Task::factory()->count(3)->create(['status' => 'done']);
        Task::factory()->count(2)->create(['status' => 'todo']);

        $response = $this->get('/');
        $response->assertStatus(200);
    });

    test('landing page works with empty database', function () {
        $this->get('/')->assertStatus(200);
    });
});
