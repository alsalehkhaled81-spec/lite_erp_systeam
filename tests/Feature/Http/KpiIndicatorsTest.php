<?php

use App\Models\Client;
use App\Models\Task;
use App\Models\Project;
use App\Models\Invoice;
use App\Filament\Widgets\AdminKpiIndicators;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Carbon\Carbon;

describe('Admin KPI Indicators', function () {

    function createKpiWidget(): AdminKpiIndicators
    {
        $reflection = new ReflectionClass(AdminKpiIndicators::class);
        $widget = $reflection->newInstanceWithoutConstructor();

        return $widget;
    }

    function invokeProtected(AdminKpiIndicators $widget, string $method): mixed
    {
        $reflection = new ReflectionClass(AdminKpiIndicators::class);
        $method = $reflection->getMethod($method);
        $method->setAccessible(true);

        return $method->invoke($widget);
    }

    function getStatsArray(AdminKpiIndicators $widget): array
    {
        return invokeProtected($widget, 'getStats');
    }

    function invokePrivate(AdminKpiIndicators $widget, string $method): array
    {
        $reflection = new ReflectionClass(AdminKpiIndicators::class);
        $method = $reflection->getMethod($method);
        $method->setAccessible(true);

        return $method->invoke($widget);
    }

    test('task completion rate is calculated correctly', function () {
        $project = Project::factory()->create();
        $employee = \App\Models\Employee::factory()->create();

        Task::factory()->create(['status' => 'todo', 'project_id' => $project->id, 'employee_id' => $employee->id]);
        Task::factory()->create(['status' => 'todo', 'project_id' => $project->id, 'employee_id' => $employee->id]);
        Task::factory()->create(['status' => 'done', 'project_id' => $project->id, 'employee_id' => $employee->id]);
        Task::factory()->create(['status' => 'done', 'project_id' => $project->id, 'employee_id' => $employee->id]);

        $widget = createKpiWidget();
        [$rate, $trend] = invokePrivate($widget, 'computeTaskCompletion');

        expect($rate)->toBe(50.0);
        expect($trend)->toBeArray()->toHaveCount(6);
    });

    test('task completion rate is 0 when no tasks exist', function () {
        $widget = createKpiWidget();
        [$rate, $trend] = invokePrivate($widget, 'computeTaskCompletion');

        expect($rate)->toBe(0);
        expect($trend)->toBe([0, 0, 0, 0, 0, 0]);
    });

    test('task completion trend reflects monthly completion ratio', function () {
        $project = Project::factory()->create();
        $employee = \App\Models\Employee::factory()->create();

        $lastMonth = now()->subMonth();

        Task::factory()->create([
            'status' => 'done',
            'project_id' => $project->id,
            'employee_id' => $employee->id,
            'created_at' => $lastMonth->copy()->startOfMonth(),
        ]);
        Task::factory()->create([
            'status' => 'todo',
            'project_id' => $project->id,
            'employee_id' => $employee->id,
            'created_at' => $lastMonth->copy()->startOfMonth(),
        ]);

        Task::factory()->create([
            'status' => 'done',
            'project_id' => $project->id,
            'employee_id' => $employee->id,
            'created_at' => now(),
        ]);

        $widget = createKpiWidget();
        [$rate, $trend] = invokePrivate($widget, 'computeTaskCompletion');

        $lastMonthIndex = 4;

        expect($trend[$lastMonthIndex])->toBe(50.0)
            ->and($trend[5])->toBe(100.0);
    });

    test('average project duration is calculated from completed projects', function () {
        Project::factory()->create([
            'status' => 'completed',
            'start_date' => '2026-01-01',
            'end_date' => '2026-01-11',
        ]);
        Project::factory()->create([
            'status' => 'completed',
            'start_date' => '2026-02-01',
            'end_date' => '2026-02-21',
        ]);
        Project::factory()->create([
            'status' => 'in_progress',
            'start_date' => '2026-03-01',
            'end_date' => '2026-03-15',
        ]);

        $widget = createKpiWidget();
        [$avgDuration, $trend, $completedCount] = invokePrivate($widget, 'computeProjectDuration');

        expect($avgDuration)->toBe(15.0)
            ->and($completedCount)->toBe(2);
    });

    test('average project duration is 0 when no completed projects with dates exist', function () {
        Project::factory()->create([
            'status' => 'in_progress',
            'start_date' => '2026-01-01',
            'end_date' => '2026-01-11',
        ]);

        $widget = createKpiWidget();
        [$avgDuration, $trend, $completedCount] = invokePrivate($widget, 'computeProjectDuration');

        expect($avgDuration)->toBe(0)
            ->and($completedCount)->toBe(0);
    });

    test('project duration trend shows real project durations', function () {
        Project::factory()->create(['start_date' => '2026-01-01', 'end_date' => '2026-01-31', 'status' => 'completed']);
        Project::factory()->create(['start_date' => '2026-02-01', 'end_date' => '2026-02-15', 'status' => 'completed']);

        $widget = createKpiWidget();
        [$avgDuration, $trend] = invokePrivate($widget, 'computeProjectDuration');

        expect($trend)->toBeArray();
        expect(in_array(14, $trend))->toBeTrue();
        expect(in_array(30, $trend))->toBeTrue();
    });

    test('client satisfaction rate is calculated from paid invoices', function () {
        $client = Client::factory()->create();

        Invoice::factory()->create(['status' => 'paid', 'client_id' => $client->id]);
        Invoice::factory()->create(['status' => 'paid', 'client_id' => $client->id]);
        Invoice::factory()->create(['status' => 'paid', 'client_id' => $client->id]);
        Invoice::factory()->create(['status' => 'unpaid', 'client_id' => $client->id]);

        $widget = createKpiWidget();
        [$rate, $trend] = invokePrivate($widget, 'computeClientSatisfaction');

        expect($rate)->toBe(75.0);
        expect($trend)->toBeArray()->toHaveCount(6);
    });

    test('client satisfaction rate is 0 when no invoices exist', function () {
        $widget = createKpiWidget();
        [$rate, $trend] = invokePrivate($widget, 'computeClientSatisfaction');

        expect($rate)->toBe(0);
        expect($trend)->toBe([0, 0, 0, 0, 0, 0]);
    });

    test('client satisfaction trend reflects monthly payment ratio', function () {
        $client = Client::factory()->create();
        $lastMonth = now()->subMonth();

        Invoice::factory()->create([
            'status' => 'paid',
            'client_id' => $client->id,
            'issue_date' => $lastMonth->copy()->startOfMonth(),
        ]);
        Invoice::factory()->create([
            'status' => 'unpaid',
            'client_id' => $client->id,
            'issue_date' => $lastMonth->copy()->startOfMonth(),
        ]);
        Invoice::factory()->create([
            'status' => 'paid',
            'client_id' => $client->id,
            'issue_date' => now(),
        ]);

        $widget = createKpiWidget();
        [$rate, $trend] = invokePrivate($widget, 'computeClientSatisfaction');

        expect($trend[4])->toBe(50.0)
            ->and($trend[5])->toBe(100.0);
    });

    test('getStats returns 4 Stat objects with real computed values', function () {
        $project = Project::factory()->create();
        $employee = \App\Models\Employee::factory()->create();
        $client = Client::factory()->create();

        Task::factory()->create(['status' => 'done', 'project_id' => $project->id, 'employee_id' => $employee->id]);
        Task::factory()->create(['status' => 'todo', 'project_id' => $project->id, 'employee_id' => $employee->id]);

        Invoice::factory()->create(['status' => 'paid', 'client_id' => $client->id]);

        $widget = createKpiWidget();
        $stats = getStatsArray($widget);

        expect($stats)->toHaveCount(4);
        foreach ($stats as $stat) {
            expect($stat)->toBeInstanceOf(Stat::class);
        }

        $completionLabel = invade($stats[0])->label;
        expect($completionLabel)->toBe(__('filament.widgets.task_completion_rate'));

        $completionValue = invade($stats[0])->value;
        expect($completionValue)->toBe('50%');
    });

    test('overdue tasks count is accurate', function () {
        $project = Project::factory()->create();
        $employee = \App\Models\Employee::factory()->create();

        Task::factory()->create([
            'status' => 'todo',
            'due_date' => now()->subDays(5),
            'project_id' => $project->id,
            'employee_id' => $employee->id,
        ]);
        Task::factory()->create([
            'status' => 'done',
            'due_date' => now()->subDays(5),
            'project_id' => $project->id,
            'employee_id' => $employee->id,
        ]);
        Task::factory()->create([
            'status' => 'in_progress',
            'due_date' => now()->addDays(5),
            'project_id' => $project->id,
            'employee_id' => $employee->id,
        ]);

        $widget = createKpiWidget();
        $stats = getStatsArray($widget);

        $overdueValue = invade($stats[3])->value;
        expect((string) $overdueValue)->toBe('1');
    });
});
