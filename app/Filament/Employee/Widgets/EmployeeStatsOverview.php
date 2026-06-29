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

        $employee = auth()->user()->employee;
        $remainingLeaves = $employee ? $employee->remaining_leave_balance : 0;
        $totalProjects = $employee ? $employee->projects()->count() : 0;

        return[
            Stat::make(__('filament.emp_stats_total'), $total)
                ->description(__('filament.emp_stats_total_desc'))
                ->descriptionIcon('heroicon-m-clipboard-document-list')
                ->chart([3, 5, 4, 6, 5, $total])
                ->color('primary')
                ->extraAttributes(['class' => 'cursor-pointer transition-all duration-300']),

            Stat::make(__('الرصيد المتبقي للإجازات'), $remainingLeaves . ' ' . __('يوم'))
                ->description(__('من أصل') . ' ' . ($employee ? $employee->annual_leave_balance : 0) . ' ' . __('يوم'))
                ->descriptionIcon('heroicon-m-calendar-days')
                ->chart([2, 4, 3, 5, 4, $remainingLeaves])
                ->color('success')
                ->extraAttributes(['class' => 'cursor-pointer transition-all duration-300']),

            Stat::make(__('المشاريع النشطة'), $totalProjects)
                ->description(__('إجمالي المشاريع الموكلة إليك'))
                ->descriptionIcon('heroicon-m-briefcase')
                ->chart([1, 2, 3, 4, 5, $totalProjects])
                ->color('warning')
                ->extraAttributes(['class' => 'cursor-pointer transition-all duration-300']),
        ];
    }
}