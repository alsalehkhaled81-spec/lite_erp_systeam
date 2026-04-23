<?php

namespace App\Filament\Employee\Widgets;

use App\Models\Task;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class EmployeeStatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $employeeId = auth()->user()->employee->id ?? null;

        if (!$employeeId) {
            return [];
        }

        return[
            Stat::make('مهامي الإجمالية', Task::where('employee_id', $employeeId)->count())
                ->color('primary'),
            Stat::make('قيد التنفيذ', Task::where('employee_id', $employeeId)->where('status', 'in_progress')->count())
                ->color('warning'),
            Stat::make('المنتهية', Task::where('employee_id', $employeeId)->where('status', 'done')->count())
                ->color('success'),
        ];
    }
}
