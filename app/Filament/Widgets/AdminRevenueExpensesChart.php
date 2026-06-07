<?php

namespace App\Filament\Widgets;

use App\Models\Invoice;
use App\Models\Expense;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class AdminRevenueExpensesChart extends ChartWidget
{
    protected static ?int $sort = 5;
    protected static ?string $maxHeight = '300px';

    public function getHeading(): string
    {
        return __('filament.widgets.revenue_expenses_chart');
    }

    protected function getData(): array
    {
        $months = [];
        $revenueData = [];
        $expensesData = [];

        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $monthKey = $date->format('Y-m');
            $monthLabel = $date->translatedFormat('M Y');

            $months[] = $monthLabel;

            $revenue = Invoice::where('status', 'paid')
                ->whereYear('issue_date', $date->year)
                ->whereMonth('issue_date', $date->month)
                ->sum('amount');

            $expenses = Expense::whereYear('expense_date', $date->year)
                ->whereMonth('expense_date', $date->month)
                ->sum('amount');

            $revenueData[] = round($revenue, 2);
            $expensesData[] = round($expenses, 2);
        }

        return [
            'datasets' => [
                [
                    'label' => __('filament.widgets.total_income'),
                    'data' => $revenueData,
                    'borderColor' => 'rgba(16, 185, 129, 1)',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.15)',
                    'fill' => true,
                    'tension' => 0.4,
                    'pointRadius' => 4,
                    'pointHoverRadius' => 7,
                ],
                [
                    'label' => __('filament.widgets.total_expenses'),
                    'data' => $expensesData,
                    'borderColor' => 'rgba(239, 68, 68, 1)',
                    'backgroundColor' => 'rgba(239, 68, 68, 0.15)',
                    'fill' => true,
                    'tension' => 0.4,
                    'pointRadius' => 4,
                    'pointHoverRadius' => 7,
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
                    'labels' => [
                        'padding' => 16,
                        'usePointStyle' => true,
                        'pointStyle' => 'circle',
                    ],
                ],
                'tooltip' => [
                    'mode' => 'index',
                    'intersect' => false,
                ],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'callback' => 'function(value) { return "$" + value.toLocaleString(); }',
                    ],
                ],
            ],
            'interaction' => [
                'mode' => 'nearest',
                'axis' => 'x',
                'intersect' => false,
            ],
        ];
    }
}