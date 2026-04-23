<?php

namespace App\Filament\Accountant\Widgets;

use App\Models\Invoice;
use App\Models\Expense;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class FinanceStatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $totalIncome = Invoice::where('status', 'paid')->sum('amount');
        $totalExpenses = Expense::sum('amount');
        $netProfit = $totalIncome - $totalExpenses;

        return[
            Stat::make('الإيرادات (الفواتير المدفوعة)', '$' . number_format($totalIncome, 2))
                ->color('success'),
            Stat::make('المصروفات', '$' . number_format($totalExpenses, 2))
                ->color('danger'),
            Stat::make('صافي الربح', '$' . number_format($netProfit, 2))
                ->description($netProfit > 0 ? 'ربح ممتاز' : 'خسارة')
                ->descriptionIcon($netProfit > 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($netProfit > 0 ? 'success' : 'danger'),
        ];
    }
}
