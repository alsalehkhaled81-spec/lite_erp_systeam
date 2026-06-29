<?php

namespace App\Filament\Client\Widgets;

use App\Models\Invoice;
use App\Models\Project;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ClientStatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $clientId = auth('client')->id();

        $totalProjects = Project::where('client_id', $clientId)->count();
        $activeProjects = Project::where('client_id', $clientId)->where('status', 'in_progress')->count();
        
        $completedTasks = \App\Models\Task::whereHas('project', function ($q) use ($clientId) {
            $q->where('client_id', $clientId);
        })->where('status', 'completed')->count();

        $unpaidInvoices = Invoice::where('client_id', $clientId)->whereIn('status', ['unpaid', 'overdue'])->sum('total_with_vat');
        $paidInvoices = Invoice::where('client_id', $clientId)->where('status', 'paid')->sum('total_with_vat');

        return [
            Stat::make(__('filament.client_portal.total_projects'), $totalProjects)
                ->description(__('filament.client_portal.total_projects_desc'))
                ->descriptionIcon('heroicon-m-briefcase')
                ->color('primary')
                ->extraAttributes(['class' => 'cursor-pointer transition-all duration-300']),

            Stat::make(__('filament.client_portal.active_projects'), $activeProjects)
                ->description(__('filament.client_portal.active_projects_desc'))
                ->descriptionIcon('heroicon-m-arrow-path')
                ->color('warning')
                ->extraAttributes(['class' => 'cursor-pointer transition-all duration-300']),

            Stat::make('المهام المنجزة', $completedTasks)
                ->description('إجمالي المهام المكتملة في مشاريعك')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success')
                ->extraAttributes(['class' => 'cursor-pointer transition-all duration-300']),

            Stat::make('المدفوعات السابقة', '$'.number_format($paidInvoices, 2))
                ->description('إجمالي الفواتير المدفوعة')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success')
                ->extraAttributes(['class' => 'cursor-pointer transition-all duration-300']),

            Stat::make(__('filament.client_portal.outstanding_balance'), '$'.number_format($unpaidInvoices, 2))
                ->description(__('filament.client_portal.outstanding_balance_desc'))
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('danger')
                ->extraAttributes(['class' => 'cursor-pointer transition-all duration-300']),
        ];
    }
}
