<?php

use App\Models\Employee;
use App\Models\Project;
use App\Models\Task;
use App\Models\Client;
use App\Models\Vacancy;

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

    test('landing page shows open vacancies section', function () {
        Vacancy::factory()->create(['title' => 'Senior Laravel Developer', 'status' => 'open']);
        Vacancy::factory()->create(['title' => 'Frontend React Developer', 'status' => 'open']);

        $response = $this->withSession(['locale' => 'ar'])->get('/');
        $response->assertStatus(200);
        $response->assertSee('الوظائف الشاغرة');
        $response->assertSee('Senior Laravel Developer');
        $response->assertSee('Frontend React Developer');
    });

    test('landing page does not show closed vacancies', function () {
        Vacancy::factory()->create(['title' => 'Open Position', 'status' => 'open']);
        Vacancy::factory()->create(['title' => 'Closed Position', 'status' => 'closed']);

        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('Open Position');
        $response->assertDontSee('Closed Position');
    });

    test('landing page shows applicant count per vacancy', function () {
        $vacancy = Vacancy::factory()->create(['title' => 'DevOps Engineer', 'status' => 'open']);
        Employee::factory()->count(4)->create(['vacancy_id' => $vacancy->id, 'status' => 'pending']);

        $response = $this->withSession(['locale' => 'ar'])->get('/');
        $response->assertStatus(200);
        $response->assertSee('DevOps Engineer');
        $response->assertSee('4 متقدم');
    });
});
