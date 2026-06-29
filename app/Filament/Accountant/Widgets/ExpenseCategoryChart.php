<?php

namespace App\Filament\Accountant\Widgets;

use App\Models\Expense;
use Filament\Widgets\ChartWidget;
use Carbon\Carbon;

class ExpenseCategoryChart extends ChartWidget
{
    protected static ?int $sort = 7;
    protected static ?string $maxHeight = '300px';

    public function getHeading(): string
    {
        return __('filament.widgets.expense_categories');
    }

    protected function getData(): array
    {
        $sixMonthsAgo = Carbon::now()->subMonths(6)->startOfMonth();

        $categories = Expense::whereNotNull('category')
            ->where('expense_date', '>=', $sixMonthsAgo)
            ->selectRaw('category, SUM(amount) as total')
            ->groupBy('category')
            ->orderByDesc('total')
            ->limit(8)
            ->get();

        $labels = $categories->pluck('category')->toArray();
        $data = $categories->pluck('total')->map(fn ($v) => round((float) $v, 2))->toArray();

        $colors = [
            'rgba(99, 102, 241, 0.8)',
            'rgba(16, 185, 129, 0.8)',
            'rgba(245, 158, 11, 0.8)',
            'rgba(239, 68, 68, 0.8)',
            'rgba(168, 85, 247, 0.8)',
            'rgba(59, 130, 246, 0.8)',
            'rgba(236, 72, 153, 0.8)',
            'rgba(20, 184, 166, 0.8)',
        ];

        return [
            'datasets' => [
                [
                    'label' => __('filament.widgets.amount'),
                    'data' => $data,
                    'backgroundColor' => $colors,
                    'borderColor' => array_map(fn ($c) => str_replace('0.8', '1', $c), $colors),
                    'borderWidth' => 2,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'position' => 'right',
                    'labels' => ['padding' => 10, 'usePointStyle' => true, 'boxWidth' => 12],
                ],
                'tooltip' => [
                    'callbacks' => [
                        'label' => 'function(context) { return context.label + ": $" + Number(context.parsed).toLocaleString(undefined, {minimumFractionDigits: 0, maximumFractionDigits: 0}); }',
                    ],
                ],
            ],
            'cutout' => '55%',
            'scales' => [],
        ];
    }
}
