<?php

namespace App\Filament\Pm\Widgets;

use App\Models\Project;
use App\Models\Task;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class PmStatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $totalProjects = Project::count();
        $inProgress = Task::where('status', 'in_progress')->count();
        $done = Task::where('status', 'done')->count();

        return [
            Stat::make(__('filament.widgets.total_projects'), $totalProjects)
                ->description(__('filament.widgets.total_projects_desc'))
                ->descriptionIcon('heroicon-m-rectangle-stack')
                ->chart([3, 5, 4, 6, 7, $totalProjects])
                ->color('primary')
                ->extraAttributes(['class' => 'cursor-pointer transition-all duration-300']),

            Stat::make(__('filament.widgets.tasks_in_progress'), $inProgress)
                ->description(__('filament.widgets.tasks_in_progress_desc'))
                ->descriptionIcon('heroicon-m-arrow-path')
                ->chart([2, 4, 3, 5, 4, $inProgress])
                ->color('warning')
                ->extraAttributes(['class' => 'cursor-pointer transition-all duration-300']),

            Stat::make(__('filament.widgets.tasks_done'), $done)
                ->description(__('filament.widgets.tasks_done_desc'))
                ->descriptionIcon('heroicon-m-check-badge')
                ->chart([1, 3, 5, 4, 6, $done])
                ->color('success')
                ->extraAttributes(['class' => 'cursor-pointer transition-all duration-300']),
        ];
    }
}
