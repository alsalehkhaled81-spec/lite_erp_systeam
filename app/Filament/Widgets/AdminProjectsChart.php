<?php

namespace App\Filament\Widgets;

use App\Models\Project;
use Filament\Widgets\ChartWidget;

class AdminProjectsChart extends ChartWidget
{
    protected static ?int $sort = 2;
    protected static ?string $maxHeight = '280px';

    public function getHeading(): string
    {
        return __('filament.widgets.project_status');
    }

    protected function getData(): array
    {
        return [
            'datasets' => [[
                'label' => __('filament.model.projects'),
                'data' => [
                    Project::where('status', 'pending')->count(),
                    Project::where('status', 'in_progress')->count(),
                    Project::where('status', 'completed')->count(),
                    Project::where('status', 'canceled')->count(),
                ],
                'backgroundColor' => [
                    'rgba(99, 102, 241, 0.85)',
                    'rgba(245, 158, 11, 0.85)',
                    'rgba(16, 185, 129, 0.85)',
                    'rgba(239, 68, 68, 0.85)',
                ],
                'borderColor' => [
                    'rgb(99, 102, 241)',
                    'rgb(245, 158, 11)',
                    'rgb(16, 185, 129)',
                    'rgb(239, 68, 68)',
                ],
                'borderWidth' => 2,
                'hoverOffset' => 8,
            ]],
            'labels' => [
                __('filament.status.pending'),
                __('filament.status.in_progress'),
                __('filament.status.completed'),
                __('filament.status.canceled'),
            ],
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
                    'position' => 'bottom',
                    'labels' => ['padding' => 16, 'usePointStyle' => true, 'pointStyle' => 'circle'],
                ],
            ],
            'cutout' => '65%',
        ];
    }
}
