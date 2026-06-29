<?php

namespace App\Filament\Hr\Widgets;

use App\Models\Employee;
use Filament\Widgets\ChartWidget;

class EmployeesChart extends ChartWidget
{
    protected static ?int $sort = 2;
    protected static ?string $maxHeight = '280px';

    public function getHeading(): string
    {
        return __('filament.widgets.employees_distribution');
    }

    protected function getData(): array
    {
        return [
            'datasets' => [[
                'label' => __('filament.widgets.employees'),
                'data' => [
                    Employee::where('status', 'active')->count(),
                    Employee::where('status', 'on_leave')->count(),
                    Employee::where('status', 'terminated')->count(),
                ],
                'backgroundColor' => [
                    'rgba(16, 185, 129, 0.85)',
                    'rgba(245, 158, 11, 0.85)',
                    'rgba(239, 68, 68, 0.85)',
                ],
                'borderColor' => [
                    'rgb(16, 185, 129)',
                    'rgb(245, 158, 11)',
                    'rgb(239, 68, 68)',
                ],
                'borderWidth' => 2,
                'hoverOffset' => 8,
            ]],
            'labels' => [
                __('filament.status.active'),
                __('filament.status.on_leave'),
                __('filament.status.terminated'),
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
                    'labels' => [
                        'padding' => 16,
                        'usePointStyle' => true,
                    ],
                ],
            ],
            'scales' => [
                'x' => [
                    'display' => false,
                ],
                'y' => [
                    'display' => false,
                ],
            ],
            'cutout' => '70%',
        ];
    }
}
