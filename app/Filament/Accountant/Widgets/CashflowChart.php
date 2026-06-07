<?php

namespace App\Filament\Accountant\Widgets;

use App\Models\Invoice;
use App\Models\Expense;
use Filament\Widgets\ChartWidget;
use Carbon\Carbon;

class CashflowChart extends ChartWidget
{
    protected static ?int $sort = 2;
    protected static ?string $maxHeight = '300px';

    public function getHeading(): string
    {
        return __('filament.widgets.monthly_cashflow');
    }

    protected function getData(): array
    {
        $months = [];
        $incomeData = [];
        $expenseData = [];
        $netData = [];

        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $months[] = $date->translatedFormat('M Y');
            $income = (float) Invoice::where('status', 'paid')
                ->whereYear('issue_date', $date->year)
                ->whereMonth('issue_date', $date->month)
                ->sum('amount');
            $expense = (float) Expense::whereYear('expense_date', $date->year)
                ->whereMonth('expense_date', $date->month)
                ->sum('amount');
            $incomeData[] = $income;
            $expenseData[] = $expense;
            $netData[] = $income - $expense;
        }

        return [
            'datasets' => [
                [
                    'label' => __('filament.widgets.inflow'),
                    'data' => $incomeData,
                    'backgroundColor' => 'rgba(16, 185, 129, 0.7)',
                    'borderColor' => 'rgba(16, 185, 129, 1)',
                    'borderWidth' => 1,
                ],
                [
                    'label' => __('filament.widgets.outflow'),
                    'data' => $expenseData,
                    'backgroundColor' => 'rgba(239, 68, 68, 0.7)',
                    'borderColor' => 'rgba(239, 68, 68, 1)',
                    'borderWidth' => 1,
                ],
                [
                    'label' => __('filament.widgets.net_flow'),
                    'data' => $netData,
                    'type' => 'line',
                    'borderColor' => 'rgba(99, 102, 241, 1)',
                    'backgroundColor' => 'rgba(99, 102, 241, 0.1)',
                    'fill' => true,
                    'tension' => 0.4,
                    'pointRadius' => 3,
                ],
            ],
            'labels' => $months,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'position' => 'top',
                    'labels' => ['padding' => 12, 'usePointStyle' => true],
                ],
            ],
            'scales' => [
                'y' => ['beginAtZero' => true],
            ],
        ];
    }
}
