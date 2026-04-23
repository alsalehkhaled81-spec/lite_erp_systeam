<?php

namespace App\Filament\Widgets;

use App\Models\Employee;
use App\Models\Project;
use App\Models\Invoice;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AdminStatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return[
            Stat::make('إجمالي الموظفين', Employee::count())
                ->description('المسجلين في النظام')
                ->descriptionIcon('heroicon-m-users')
                ->color('primary'),

            Stat::make('المشاريع النشطة', Project::where('status', 'in_progress')->count())
                ->description('مشاريع قيد التنفيذ')
                ->descriptionIcon('heroicon-m-briefcase')
                ->color('warning'),

            Stat::make('إجمالي الأرباح المحصلة', '$' . number_format(Invoice::where('status', 'paid')->sum('amount'), 2))
                ->description('الفواتير المدفوعة فقط')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'),
        ];
    }
}
