<?php

namespace App\Filament\Hr\Widgets;

use App\Models\Employee;
use App\Models\Resume;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class HrStatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $active = Employee::where('status', 'active')->count();
        $onLeave = Employee::where('status', 'on_leave')->count();
        $resumes = Resume::count();

        return [
            Stat::make(__('filament.widgets.active_employees'), $active)
                ->description(__('filament.widgets.active_employees_desc'))
                ->descriptionIcon('heroicon-m-user-group')
                ->chart([5, 6, 7, 6, 8, $active])
                ->color('success')
                ->extraAttributes(['class' => 'cursor-pointer transition-all duration-300']),

            Stat::make(__('filament.widgets.on_leave_employees'), $onLeave)
                ->description(__('filament.widgets.on_leave_employees_desc'))
                ->descriptionIcon('heroicon-m-calendar-days')
                ->chart([2, 3, 1, 4, 2, $onLeave])
                ->color('warning')
                ->extraAttributes(['class' => 'cursor-pointer transition-all duration-300']),

            Stat::make(__('filament.widgets.resumes_uploaded'), $resumes)
                ->description(__('filament.widgets.resumes_uploaded_desc'))
                ->descriptionIcon('heroicon-m-cpu-chip')
                ->chart([1, 3, 2, 5, 4, $resumes])
                ->color('info')
                ->extraAttributes(['class' => 'cursor-pointer transition-all duration-300']),
        ];
    }
}
