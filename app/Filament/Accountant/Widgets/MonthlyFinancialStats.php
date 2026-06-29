<?php

namespace App\Filament\Accountant\Widgets;

use App\Models\Invoice;
use App\Models\Expense;
use App\Models\Payroll;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Carbon\Carbon;

class MonthlyFinancialStats extends StatsOverviewWidget
{
    protected static ?int $sort = 0;

    protected function getStats(): array
    {
        $now = Carbon::now();
        $lastMonth = Carbon::now()->subMonth();

        $thisMonthRevenue = (float) Invoice::where('status', 'paid')
            ->whereYear('issue_date', $now->year)
            ->whereMonth('issue_date', $now->month)
            ->sum('amount');

        $lastMonthRevenue = (float) Invoice::where('status', 'paid')
            ->whereYear('issue_date', $lastMonth->year)
            ->whereMonth('issue_date', $lastMonth->month)
            ->sum('amount');

        $revenueChange = $lastMonthRevenue > 0
            ? round((($thisMonthRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100, 1)
            : 0;

        $thisMonthExpenses = (float) Expense::whereYear('expense_date', $now->year)
            ->whereMonth('expense_date', $now->month)
            ->sum('amount');

        $lastMonthExpenses = (float) Expense::whereYear('expense_date', $lastMonth->year)
            ->whereMonth('expense_date', $lastMonth->month)
            ->sum('amount');

        $expenseChange = $lastMonthExpenses > 0
            ? round((($thisMonthExpenses - $lastMonthExpenses) / $lastMonthExpenses) * 100, 1)
            : 0;

        $thisMonthPayroll = (float) Payroll::where('month_year', $now->format('Y-m'))->sum('net_salary');

        $thisMonthProfit = $thisMonthRevenue - $thisMonthExpenses - $thisMonthPayroll;

        $pendingInvoices = (float) Invoice::whereIn('status', ['unpaid', 'overdue'])->sum('total_with_vat');

        $monthlyRevenue6mo = [];
        for ($i = 5; $i >= 0; $i--) {
            $d = Carbon::now()->subMonths($i);
            $monthlyRevenue6mo[] = (int) round(Invoice::where('status', 'paid')
                ->whereYear('issue_date', $d->year)
                ->whereMonth('issue_date', $d->month)
                ->sum('amount'));
        }

        $monthlyExpense6mo = [];
        for ($i = 5; $i >= 0; $i--) {
            $d = Carbon::now()->subMonths($i);
            $monthlyExpense6mo[] = (int) round(Expense::whereYear('expense_date', $d->year)
                ->whereMonth('expense_date', $d->month)
                ->sum('amount'));
        }

        return [
            Stat::make(__('filament.widgets.this_month_revenue'), '$' . number_format($thisMonthRevenue, 0))
                ->description($revenueChange >= 0 ? __('filament.widgets.up_from_last_month', ['val' => abs($revenueChange) . '%']) : __('filament.widgets.down_from_last_month', ['val' => abs($revenueChange) . '%']))
                ->descriptionIcon($revenueChange >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->chart($monthlyRevenue6mo)
                ->color($revenueChange >= 0 ? 'success' : 'danger'),

            Stat::make(__('filament.widgets.this_month_expenses'), '$' . number_format($thisMonthExpenses, 0))
                ->description($expenseChange >= 0 ? __('filament.widgets.up_from_last_month', ['val' => abs($expenseChange) . '%']) : __('filament.widgets.down_from_last_month', ['val' => abs($expenseChange) . '%']))
                ->descriptionIcon($expenseChange >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->chart($monthlyExpense6mo)
                ->color('danger'),

            Stat::make(__('filament.widgets.this_month_profit'), '$' . number_format($thisMonthProfit, 0))
                ->description($thisMonthProfit >= 0 ? __('filament.widgets.profitable') : __('filament.widgets.loss'))
                ->descriptionIcon($thisMonthProfit >= 0 ? 'heroicon-m-banknotes' : 'heroicon-m-exclamation-triangle')
                ->color($thisMonthProfit >= 0 ? 'success' : 'danger'),

            Stat::make(__('filament.widgets.pending_invoices'), '$' . number_format($pendingInvoices, 0))
                ->description(__('filament.widgets.awaiting_payment'))
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),
        ];
    }
}
