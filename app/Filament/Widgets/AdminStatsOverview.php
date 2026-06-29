<?php

namespace App\Filament\Widgets;

use App\Models\Employee;
use App\Models\Project;
use App\Models\Invoice;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AdminStatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $employeeCount = Employee::count();
        $activeProjects = Project::where('status', 'in_progress')->count();
        $totalRevenue = Invoice::where('status', 'paid')->sum('amount');
        $totalBilled = Invoice::sum('amount');
        $revenuePercentage = $totalBilled > 0
            ? round(($totalRevenue / $totalBilled) * 100, 1)
            : 0;

        $monthlyRates = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $monthBilled = Invoice::whereYear('issue_date', $month->year)
                ->whereMonth('issue_date', $month->month)
                ->sum('amount');
            $monthCollected = Invoice::where('status', 'paid')
                ->whereYear('issue_date', $month->year)
                ->whereMonth('issue_date', $month->month)
                ->sum('amount');
            $monthlyRates[] = $monthBilled > 0
                ? (int) round(($monthCollected / $monthBilled) * 100)
                : 0;
        }

        return [
            Stat::make(__('filament.widgets.total_employees'), $employeeCount)
                ->description(__('filament.widgets.total_employees_desc'))
                ->descriptionIcon('heroicon-m-users')
                ->chart([7, 3, 4, 5, 6, $employeeCount])
                ->color('primary')
                ->extraAttributes(['class' => 'cursor-pointer transition-all duration-300']),

            Stat::make(__('filament.widgets.active_projects'), $activeProjects)
                ->description(__('filament.widgets.active_projects_desc'))
                ->descriptionIcon('heroicon-m-briefcase')
                ->chart([2, 4, 3, 5, 4, $activeProjects])
                ->color('warning')
                ->extraAttributes(['class' => 'cursor-pointer transition-all duration-300']),

            Stat::make(__('filament.widgets.total_revenue'), '$' . number_format($totalRevenue, 2))
                ->description(__('filament.widgets.total_revenue_desc'))
                ->descriptionIcon('heroicon-m-banknotes')
                ->chart([3, 5, 7, 4, 6, 8])
                ->color('success')
                ->extraAttributes(['class' => 'cursor-pointer transition-all duration-300']),

            Stat::make(__('filament.widgets.revenue_percentage'), $revenuePercentage . '%')
                ->description(__('filament.widgets.revenue_percentage_desc'))
                ->descriptionIcon('heroicon-m-chart-pie')
                ->chart($monthlyRates)
                ->color('info')
                ->extraAttributes(['class' => 'cursor-pointer transition-all duration-300']),
        ];
    }
}
