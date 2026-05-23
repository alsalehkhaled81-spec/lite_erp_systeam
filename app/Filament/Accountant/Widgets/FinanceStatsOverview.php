<?php

namespace App\Filament\Accountant\Widgets;

use App\Models\Invoice;
use App\Models\Expense;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class FinanceStatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $totalIncome = Invoice::where('status', 'paid')->sum('amount');
        $totalExpenses = Expense::sum('amount');
        $netProfit = $totalIncome - $totalExpenses;

        return[
            Stat::make(__('filament.widgets.total_income'), '$' . number_format($totalIncome, 2))
                ->description(__('filament.widgets.total_income_desc'))
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->chart([4, 6, 5, 8, 7, 9])
                ->color('success')
                ->extraAttributes([
                    'class' => 'cursor-pointer transition-all duration-300',
                ]),

            Stat::make(__('filament.widgets.total_expenses'), '$' . number_format($totalExpenses, 2))
                ->description(__('filament.widgets.total_expenses_desc'))
                ->descriptionIcon('heroicon-m-arrow-trending-down')
                ->chart([3, 5, 4, 6, 7, 5])
                ->color('danger')
                ->extraAttributes([
                    'class' => 'cursor-pointer transition-all duration-300',
                ]),

            Stat::make(__('filament.widgets.net_profit'), '$' . number_format($netProfit, 2))
                ->description($netProfit > 0 ? __('filament.widgets.excellent_profit') : __('filament.widgets.loss'))
                ->descriptionIcon($netProfit > 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->chart([2, 4, 3, 6, 5, $netProfit > 0 ? 8 : 2])
                ->color($netProfit > 0 ? 'success' : 'danger')
                ->extraAttributes([
                    'class' => 'cursor-pointer transition-all duration-300',
                ]),
        ];
    }
}