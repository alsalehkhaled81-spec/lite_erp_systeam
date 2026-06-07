<?php

namespace App\Filament\Accountant\Widgets;

use App\Models\Project;
use App\Models\Expense;
use App\Models\Invoice;
use Filament\Widgets\ChartWidget;

class ProjectBudgetChart extends ChartWidget
{
    protected static ?int $sort = 4;
    protected static ?string $maxHeight = '300px';

    public function getHeading(): string
    {
        return __('filament.widgets.project_budget_vs_spent');
    }

    protected function getData(): array
    {
        $projects = Project::withSum('invoices', 'amount')
            ->orderByDesc('budget')
            ->take(10)
            ->get();

        $labels = $projects->pluck('name')->toArray();
        $budgets = $projects->pluck('budget')->map(fn ($b) => (float) ($b ?? 0))->toArray();
        $spent = $projects->map(fn ($p) => (float) ($p->invoices_sum_amount ?? 0))->toArray();

        return [
            'datasets' => [
                [
                    'label' => __('filament.widgets.planned_budget'),
                    'data' => $budgets,
                    'backgroundColor' => 'rgba(99, 102, 241, 0.7)',
                    'borderColor' => 'rgba(99, 102, 241, 1)',
                    'borderWidth' => 1,
                ],
                [
                    'label' => __('filament.widgets.actual_spent'),
                    'data' => $spent,
                    'backgroundColor' => 'rgba(245, 158, 11, 0.7)',
                    'borderColor' => 'rgba(245, 158, 11, 1)',
                    'borderWidth' => 1,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        return [
            'indexAxis' => 'y',
            'plugins' => [
                'legend' => [
                    'position' => 'top',
                    'labels' => ['padding' => 12, 'usePointStyle' => true],
                ],
            ],
            'scales' => [
                'x' => ['beginAtZero' => true],
            ],
        ];
    }
}
