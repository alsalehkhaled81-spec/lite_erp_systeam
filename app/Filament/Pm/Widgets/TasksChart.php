<?php

namespace App\Filament\Pm\Widgets;

use App\Models\Task;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;

class TasksChart extends ChartWidget
{
    use InteractsWithPageFilters;

    protected static ?int $sort = 2;
    protected static ?string $maxHeight = '280px';

    public function getHeading(): string
    {
        return __('filament.widgets.tasks_stats') ?? 'إحصائيات المهام';
    }

    protected function getData(): array
    {
        $startDate = $this->filters['startDate'] ?? null;
        $endDate = $this->filters['endDate'] ?? null;

        $query = Task::query();

        if ($startDate) {
            $query->whereDate('created_at', '>=', $startDate);
        }

        if ($endDate) {
            $query->whereDate('created_at', '<=', $endDate);
        }

        $todo = (clone $query)->where('status', 'todo')->count();
        $inProgress = (clone $query)->where('status', 'in_progress')->count();
        $review = (clone $query)->where('status', 'review')->count();
        $done = (clone $query)->where('status', 'done')->count();

        return [
            'datasets' => [[
                'label' => __('filament.widgets.tasks_count') ?? 'عدد المهام',
                'data' => [$todo, $inProgress, $review, $done],
                'backgroundColor' => [
                    'rgba(99, 102, 241, 0.75)', // todo: indigo
                    'rgba(245, 158, 11, 0.75)', // in_progress: amber
                    'rgba(59, 130, 246, 0.75)', // review: blue
                    'rgba(16, 185, 129, 0.75)', // done: emerald
                ],
                'borderColor' => [
                    'rgb(99, 102, 241)',
                    'rgb(245, 158, 11)',
                    'rgb(59, 130, 246)',
                    'rgb(16, 185, 129)',
                ],
                'borderWidth' => 2,
                'borderRadius' => 8,
                'borderSkipped' => false,
            ]],
            'labels' => [
                __('filament.status.todo') ?? 'للتنفيذ',
                __('filament.status.in_progress') ?? 'قيد التنفيذ',
                __('filament.status.review') ?? 'قيد المراجعة',
                __('filament.status.done') ?? 'مكتملة',
            ],
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
                    'display' => false,
                ],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'stepSize' => 1,
                    ],
                ],
            ],
        ];
    }
}
