<?php

namespace App\Filament\Employee\Widgets;

use App\Models\Task;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class EmployeeStatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $employeeId = auth()->user()->employee->id ?? null;

        if (!$employeeId) {
            return [];
        }

        $total = Task::where('employee_id', $employeeId)->count();
        $inProgress = Task::where('employee_id', $employeeId)->where('status', 'in_progress')->count();
        $done = Task::where('employee_id', $employeeId)->where('status', 'done')->count();

        return[
            Stat::make(__('filament.emp_stats_total'), $total)
                ->description(__('filament.emp_stats_total_desc'))
                ->descriptionIcon('heroicon-m-clipboard-document-list')
                ->chart([3, 5, 4, 6, 5, $total])
                ->color('primary')
                ->extraAttributes([
                    'class' => 'cursor-pointer transition-all duration-300',
                ]),

            Stat::make(__('filament.emp_stats_in_progress'), $inProgress)
                ->description(__('filament.emp_stats_in_progress_desc'))
                ->descriptionIcon('heroicon-m-clock')
                ->chart([2, 3, 4, 3, 5, $inProgress])
                ->color('warning')
                ->extraAttributes([
                    'class' => 'cursor-pointer transition-all duration-300',
                ]),

            Stat::make(__('filament.emp_stats_done'), $done)
                ->description(__('filament.emp_stats_done_desc'))
                ->descriptionIcon('heroicon-m-check-circle')
                ->chart([1, 2, 3, 4, 5, $done])
                ->color('success')
                ->extraAttributes([
                    'class' => 'cursor-pointer transition-all duration-300',
                ]),
        ];
    }
}