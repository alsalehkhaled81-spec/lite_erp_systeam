<?php

namespace App\Filament\Accountant\Widgets;

use App\Models\Invoice;
use App\Models\Expense;
use App\Models\Payroll;
use Filament\Widgets\ChartWidget;
use Carbon\Carbon;

class MonthlyProfitLossChart extends ChartWidget
{
    protected static ?int $sort = 6;
    protected static ?string $maxHeight = '300px';

    public function getHeading(): string
    {
        return __('filament.widgets.monthly_pl');
    }

    protected function getData(): array
    {
        $months = [];
        $revenueData = [];
        $costData = [];
        $profitData = [];

        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $months[] = $date->translatedFormat('M Y');

            $revenue = (float) Invoice::where('status', 'paid')
                ->whereYear('issue_date', $date->year)
                ->whereMonth('issue_date', $date->month)
                ->sum('amount');

            $expenses = (float) Expense::whereYear('expense_date', $date->year)
                ->whereMonth('expense_date', $date->month)
                ->sum('amount');

            $payrollStr = $date->format('Y-m');
            $payroll = (float) Payroll::where('month_year', $payrollStr)->sum('net_salary');

            $totalCost = $expenses + $payroll;
            $profit = $revenue - $totalCost;

            $revenueData[] = round($revenue, 2);
            $costData[] = round($totalCost, 2);
            $profitData[] = round($profit, 2);
        }

        return [
            'datasets' => [
                [
                    'label' => __('filament.widgets.revenue'),
                    'data' => $revenueData,
                    'borderColor' => 'rgba(16, 185, 129, 1)',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.15)',
                    'fill' => true,
                    'tension' => 0.4,
                    'pointRadius' => 3,
                ],
                [
                    'label' => __('filament.widgets.total_costs'),
                    'data' => $costData,
                    'borderColor' => 'rgba(239, 68, 68, 1)',
                    'backgroundColor' => 'rgba(239, 68, 68, 0.15)',
                    'fill' => true,
                    'tension' => 0.4,
                    'pointRadius' => 3,
                ],
                [
                    'label' => __('filament.widgets.net_profit'),
                    'data' => $profitData,
                    'type' => 'bar',
                    'backgroundColor' => $profitData === [] ? 'rgba(99,102,241,0.5)' : array_map(
                        fn ($v) => $v >= 0 ? 'rgba(16,185,129,0.6)' : 'rgba(239,68,68,0.6)',
                        $profitData
                    ),
                    'borderWidth' => 1,
                ],
            ],
            'labels' => $months,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'position' => 'top',
                    'labels' => ['padding' => 12, 'usePointStyle' => true],
                ],
                'tooltip' => [
                    'callbacks' => [
                        'label' => 'function(context) { return context.dataset.label + ": $" + Number(context.parsed.y).toLocaleString(undefined, {minimumFractionDigits: 0, maximumFractionDigits: 0}); }',
                    ],
                ],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => ['callback' => 'function(value) { return "$" + value.toLocaleString(); }'],
                ],
            ],
        ];
    }
}
