<?php

namespace App\Filament\Accountant\Widgets;

use App\Models\Invoice;
use App\Models\Expense;
use App\Models\Payroll;
use Filament\Widgets\Widget;
use Carbon\Carbon;

class TaxReportWidget extends Widget
{
    protected static string $view = 'filament.widgets.tax-report';
    protected static ?int $sort = 5;
    protected int | string | array $columnSpan = 1;

    public function getViewData(): array
    {
        $now = Carbon::now();
        $ytdRevenue = (float) Invoice::where('status', 'paid')
            ->whereYear('issue_date', '<=', $now->year)
            ->sum('amount');
        $ytdExpenses = (float) Expense::whereYear('expense_date', '<=', $now->year)
            ->sum('amount');
        $ytdSalaries = (float) Payroll::whereYear('month_year', '<=', $now->year)
            ->sum('net_salary');
        $grossProfit = $ytdRevenue - $ytdExpenses;
        $taxableIncome = max(0, $grossProfit - $ytdSalaries * 0.15);
        $estimatedTax = $taxableIncome * 0.15;
        $netAfterTax = $grossProfit - $estimatedTax;

        $quarterRevenue = (float) Invoice::where('status', 'paid')
            ->whereBetween('issue_date', [$now->copy()->startOfQuarter(), $now->copy()->endOfQuarter()])
            ->sum('amount');
        $quarterExpenses = (float) Expense::whereBetween('expense_date', [$now->copy()->startOfQuarter(), $now->copy()->endOfQuarter()])
            ->sum('amount');

        return [
            'ytdRevenue' => $ytdRevenue,
            'ytdExpenses' => $ytdExpenses,
            'grossProfit' => $grossProfit,
            'ytdSalaries' => $ytdSalaries,
            'taxableIncome' => $taxableIncome,
            'estimatedTax' => $estimatedTax,
            'netAfterTax' => $netAfterTax,
            'quarterRevenue' => $quarterRevenue,
            'quarterExpenses' => $quarterExpenses,
            'taxRate' => 15,
        ];
    }
}
