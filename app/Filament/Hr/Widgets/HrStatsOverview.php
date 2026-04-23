<?php

namespace App\Filament\Hr\Widgets;

use App\Models\Employee;
use App\Models\Resume;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class HrStatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return[
            Stat::make('على رأس العمل', Employee::where('status', 'active')->count())
                ->color('success'),
            Stat::make('في إجازة', Employee::where('status', 'on_leave')->count())
                ->color('warning'),
            Stat::make('السير الذاتية المرفوعة (AI)', Resume::count())
                ->color('info'),
        ];
    }
}
