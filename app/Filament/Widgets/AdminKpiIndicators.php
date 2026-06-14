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

    protected static ?string $pollingInterval = null;

    public function getHeading(): string
    {
        return __('filament.widgets.kpi_indicators');
    }

    protected function getStats(): array
    {
        [$taskRate, $taskTrend] = $this->computeTaskCompletion();
        [$avgDuration, $durationTrend, $completedCount] = $this->computeProjectDuration();
        [$satisfactionRate, $satisfactionTrend] = $this->computeClientSatisfaction();
        $overdueTasks = Task::where('due_date', '<', now())->where('status', '!=', 'done')->count();

        return [
            Stat::make(__('filament.widgets.task_completion_rate'), $taskRate . '%')
                ->description(__('filament.widgets.task_completion_rate_desc'))
                ->descriptionIcon($taskRate >= 70 ? 'heroicon-m-check-circle' : 'heroicon-m-exclamation-triangle')
                ->chart($taskTrend)
                ->color($taskRate >= 70 ? 'success' : ($taskRate >= 40 ? 'warning' : 'danger')),

            Stat::make(__('filament.widgets.avg_project_duration'), $avgDuration > 0 ? $avgDuration . ' ' . __('filament.widgets.days') : '—')
                ->description($completedCount > 0 ? __('filament.widgets.avg_project_duration_desc') : __('filament.widgets.no_data'))
                ->descriptionIcon('heroicon-m-clock')
                ->chart($durationTrend)
                ->color('info'),

            Stat::make(__('filament.widgets.client_satisfaction'), $satisfactionRate . '%')
                ->description(__('filament.widgets.client_satisfaction_desc'))
                ->descriptionIcon($satisfactionRate >= 70 ? 'heroicon-m-face-smile' : 'heroicon-m-face-frown')
                ->chart($satisfactionTrend)
                ->color($satisfactionRate >= 70 ? 'success' : ($satisfactionRate >= 40 ? 'warning' : 'danger')),

            Stat::make(__('filament.widgets.overdue_tasks'), $overdueTasks)
                ->description(__('filament.widgets.overdue_tasks_desc'))
                ->descriptionIcon($overdueTasks > 0 ? 'heroicon-m-exclamation-circle' : 'heroicon-m-check-circle')
                ->color($overdueTasks > 0 ? 'danger' : 'success'),
        ];
    }

    private function computeTaskCompletion(): array
    {
        $total = Task::count();
        $completed = Task::where('status', 'done')->count();
        $rate = $total > 0 ? round(($completed / $total) * 100, 1) : 0;

        $months = collect(range(5, 0))->map(function ($offset) {
            $date = now()->subMonths($offset);
            $monthTotal = Task::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
            $monthDone = Task::where('status', 'done')
                ->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
            return $monthTotal > 0 ? round(($monthDone / $monthTotal) * 100) : 0;
        })->values();

        return [$rate, $months->all()];
    }

    private function computeProjectDuration(): array
    {
        $projects = Project::whereNotNull('start_date')
            ->whereNotNull('end_date')
            ->get();

        if ($projects->isEmpty()) {
            return [0, [0, 0, 0, 0, 0, 0], 0];
        }

        $completed = Project::where('status', 'completed')
            ->whereNotNull('start_date')
            ->whereNotNull('end_date')
            ->get();

        $avgDuration = 0;
        if ($completed->isNotEmpty()) {
            $avgDuration = round($completed->map(fn ($p) => Carbon::parse($p->start_date)->startOfDay()->diffInDays(Carbon::parse($p->end_date)->startOfDay()))->average());
        }

        $trend = $projects->sortBy('end_date')->take(6)->map(function ($p) {
            return (int) Carbon::parse($p->start_date)->startOfDay()->diffInDays(Carbon::parse($p->end_date)->startOfDay());
        })->values()->all();

        if (empty($trend)) {
            $trend = [0, 0, 0, 0, 0, 0];
        }

        return [$avgDuration, $trend, $completed->count()];
    }

    private function computeClientSatisfaction(): array
    {
        $total = Invoice::count();
        $paid = Invoice::where('status', 'paid')->count();
        $rate = $total > 0 ? round(($paid / $total) * 100, 1) : 0;

        $months = collect(range(5, 0))->map(function ($offset) {
            $date = now()->subMonths($offset);
            $monthTotal = Invoice::whereYear('issue_date', $date->year)
                ->whereMonth('issue_date', $date->month)
                ->count();
            $monthPaid = Invoice::where('status', 'paid')
                ->whereYear('issue_date', $date->year)
                ->whereMonth('issue_date', $date->month)
                ->count();
            return $monthTotal > 0 ? round(($monthPaid / $monthTotal) * 100) : 0;
        })->values();

        return [$rate, $months->all()];
    }
}
