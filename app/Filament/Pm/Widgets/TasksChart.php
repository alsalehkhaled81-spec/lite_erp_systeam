<?php

namespace App\Filament\Pm\Widgets;

use App\Models\Task;
use Filament\Widgets\ChartWidget;

class TasksChart extends ChartWidget
{
    protected static ?int $sort = 2;
    protected static ?string $maxHeight = '280px';

    public function getHeading(): string
    {
        return __('filament.widgets.tasks_stats');
    }

    protected function getData(): array
    {
        return [
            'datasets' => [[
                'label' => __('filament.widgets.tasks_count'),
                'data' => [
                    Task::where('status', 'todo')->count(),
                    Task::where('status', 'in_progress')->count(),
                    Task::where('status', 'review')->count(),
                    Task::where('status', 'done')->count(),
                ],
                'backgroundColor' => [
                    'rgba(99, 102, 241, 0.75)',
                    'rgba(245, 158, 11, 0.75)',
                    'rgba(59, 130, 246, 0.75)',
                    'rgba(16, 185, 129, 0.75)',
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
                __('filament.status.todo'),
                __('filament.status.in_progress'),
                __('filament.status.review'),
                __('filament.status.done'),
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
                'legend' => ['display' => false],
            ],
            'scales' => [
                'x' => ['grid' => ['display' => false]],
                'y' => ['grid' => ['color' => 'rgba(0,0,0,0.04)'], 'beginAtZero' => true],
            ],
        ];
    }
}
