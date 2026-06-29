<?php

namespace App\Filament\Pm\Widgets;

use App\Models\Project;
use App\Models\Task;
use Carbon\Carbon;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class PmStatsOverview extends BaseWidget
{
    use InteractsWithPageFilters;

    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $startDate = $this->filters['startDate'] ?? null;
        $endDate = $this->filters['endDate'] ?? null;

        $projectQuery = Project::query();
        $taskQuery = Task::query();
        $overdueQuery = Task::where('status', '!=', 'done')->where('due_date', '<', now());

        if ($startDate) {
            $projectQuery->whereDate('created_at', '>=', $startDate);
            $taskQuery->whereDate('created_at', '>=', $startDate);
            $overdueQuery->whereDate('due_date', '>=', $startDate);
        }

        if ($endDate) {
            $projectQuery->whereDate('created_at', '<=', $endDate);
            $taskQuery->whereDate('created_at', '<=', $endDate);
            $overdueQuery->whereDate('due_date', '<=', $endDate);
        }

        $totalProjects = $projectQuery->count();
        $inProgress = (clone $taskQuery)->where('status', 'in_progress')->count();
        $done = (clone $taskQuery)->where('status', 'done')->count();
        $overdue = $overdueQuery->count();

        return [
            Stat::make(__('filament.widgets.total_projects') ?? 'إجمالي المشاريع', $totalProjects)
                ->description(__('filament.widgets.total_projects_desc') ?? 'المشاريع المضافة')
                ->descriptionIcon('heroicon-m-rectangle-stack')
                ->chart([3, 5, 4, 6, 7, max($totalProjects, 1)])
                ->color('primary')
                ->extraAttributes(['class' => 'cursor-pointer transition-all duration-300']),

            Stat::make(__('filament.widgets.tasks_in_progress') ?? 'مهام قيد التنفيذ', $inProgress)
                ->description(__('filament.widgets.tasks_in_progress_desc') ?? 'مهام يتم العمل عليها')
                ->descriptionIcon('heroicon-m-arrow-path')
                ->chart([2, 4, 3, 5, 4, max($inProgress, 1)])
                ->color('warning')
                ->extraAttributes(['class' => 'cursor-pointer transition-all duration-300']),

            Stat::make(__('filament.widgets.tasks_done') ?? 'المهام المنجزة', $done)
                ->description(__('filament.widgets.tasks_done_desc') ?? 'المهام التي تم إنهاؤها')
                ->descriptionIcon('heroicon-m-check-badge')
                ->chart([1, 3, 5, 4, 6, max($done, 1)])
                ->color('success')
                ->extraAttributes(['class' => 'cursor-pointer transition-all duration-300']),

            Stat::make(__('filament.widgets.overdue_tasks') ?? 'المهام المتأخرة', $overdue)
                ->description(__('filament.widgets.overdue_tasks_desc') ?? 'مهام تجاوزت تاريخ الاستحقاق')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->chart([5, 4, 6, 3, 2, max($overdue, 1)])
                ->color('danger')
                ->extraAttributes(['class' => 'cursor-pointer transition-all duration-300']),
        ];
    }
}
