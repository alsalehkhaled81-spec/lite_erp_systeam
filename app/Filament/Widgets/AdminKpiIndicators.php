<?php

namespace App\Filament\Widgets;

use App\Models\Task;
use App\Models\Project;
use App\Models\Invoice;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Carbon\Carbon;

class AdminKpiIndicators extends BaseWidget
{
    protected static ?int $sort = 3;

    public function getHeading(): string
    {
        return __('filament.widgets.kpi_indicators');
    }

    protected function getStats(): array
    {
        $totalTasks = Task::count();
        $completedTasks = Task::where('status', 'done')->count();
        $taskCompletionRate = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100, 1) : 0;

        $completedProjects = Project::where('status', 'completed')->get();
        $avgProjectDuration = $completedProjects->filter(fn ($p) => $p->start_date && $p->end_date)
            ->map(fn ($p) => Carbon::parse($p->start_date)->diffInDays(Carbon::parse($p->end_date)))
            ->average() ?? 0;
        $avgProjectDuration = round($avgProjectDuration, 1);

        $paidInvoices = Invoice::where('status', 'paid')->count();
        $totalInvoices = Invoice::count();
        $clientSatisfactionRate = $totalInvoices > 0 ? round(($paidInvoices / $totalInvoices) * 100, 1) : 0;

        $overdueTasks = Task::where('due_date', '<', now())->where('status', '!=', 'done')->count();

        $inProgressTasks = Task::where('status', 'in_progress')->count();
        $reviewTasks = Task::where('status', 'review')->count();

        $chartData = [
            $taskCompletionRate >= 70 ? 8 : 4,
            min($clientSatisfactionRate, 100) >= 70 ? 8 : 4,
            $avgProjectDuration > 0 ? 7 : 3,
            $overdueTasks == 0 ? 9 : 3,
        ];

        return [
            Stat::make(__('filament.widgets.task_completion_rate'), $taskCompletionRate . '%')
                ->description(__('filament.widgets.task_completion_rate_desc'))
                ->descriptionIcon($taskCompletionRate >= 70 ? 'heroicon-m-check-circle' : 'heroicon-m-exclamation-triangle')
                ->chart($this->generateMiniChart($taskCompletionRate))
                ->color($taskCompletionRate >= 70 ? 'success' : ($taskCompletionRate >= 40 ? 'warning' : 'danger')),

            Stat::make(__('filament.widgets.avg_project_duration'), $avgProjectDuration . ' ' . __('filament.widgets.days'))
                ->description(__('filament.widgets.avg_project_duration_desc'))
                ->descriptionIcon('heroicon-m-clock')
                ->chart($this->generateMiniChart(50))
                ->color('info'),

            Stat::make(__('filament.widgets.client_satisfaction'), $clientSatisfactionRate . '%')
                ->description(__('filament.widgets.client_satisfaction_desc'))
                ->descriptionIcon($clientSatisfactionRate >= 70 ? 'heroicon-m-face-smile' : 'heroicon-m-face-frown')
                ->chart($this->generateMiniChart($clientSatisfactionRate))
                ->color($clientSatisfactionRate >= 70 ? 'success' : 'warning'),

            Stat::make(__('filament.widgets.overdue_tasks'), $overdueTasks)
                ->description(__('filament.widgets.overdue_tasks_desc'))
                ->descriptionIcon('heroicon-m-exclamation-circle')
                ->color($overdueTasks > 0 ? 'danger' : 'success'),
        ];
    }

    private function generateMiniChart(float $percentage): array
    {
        $base = collect(range(1, 7))->map(fn () => rand(max(1, $percentage - 15), min(100, $percentage + 15)))->toArray();
        $base[] = $percentage;
        return $base;
    }
}