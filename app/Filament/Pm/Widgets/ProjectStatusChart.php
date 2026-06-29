<?php

namespace App\Filament\Pm\Widgets;

use App\Models\Project;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;

class ProjectStatusChart extends ChartWidget
{
    use InteractsWithPageFilters;

    protected static ?int $sort = 3;
    protected static ?string $maxHeight = '280px';

    public function getHeading(): string
    {
        return __('filament.widgets.project_status') ?? 'حالة المشاريع';
    }

    protected function getData(): array
    {
        $startDate = $this->filters['startDate'] ?? null;
        $endDate = $this->filters['endDate'] ?? null;

        $query = Project::query();

        if ($startDate) {
            $query->whereDate('created_at', '>=', $startDate);
        }

        if ($endDate) {
            $query->whereDate('created_at', '<=', $endDate);
        }

        $pending = (clone $query)->where('status', 'pending')->count();
        $active = (clone $query)->where('status', 'active')->count();
        $completed = (clone $query)->where('status', 'completed')->count();
        $onHold = (clone $query)->where('status', 'on_hold')->count();

        return [
            'datasets' => [[
                'label' => __('filament.widgets.projects_count') ?? 'المشاريع',
                'data' => [$pending, $active, $completed, $onHold],
                'backgroundColor' => [
                    'rgba(156, 163, 175, 0.75)', // pending: gray
                    'rgba(59, 130, 246, 0.75)', // active: blue
                    'rgba(16, 185, 129, 0.75)', // completed: emerald
                    'rgba(245, 158, 11, 0.75)', // on_hold: amber
                ],
                'borderColor' => [
                    'rgb(156, 163, 175)',
                    'rgb(59, 130, 246)',
                    'rgb(16, 185, 129)',
                    'rgb(245, 158, 11)',
                ],
                'borderWidth' => 2,
            ]],
            'labels' => [
                __('filament.status.pending') ?? 'قيد الانتظار',
                __('filament.status.active') ?? 'فعال',
                __('filament.status.completed') ?? 'مكتمل',
                __('filament.status.on_hold') ?? 'مؤجل',
            ],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
