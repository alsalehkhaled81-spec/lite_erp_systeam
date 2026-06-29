<?php

namespace App\Filament\Client\Widgets;

use App\Models\Project;
use Filament\Widgets\ChartWidget;

class ClientProjectsChart extends ChartWidget
{
    protected static ?string $heading = null;

    protected static ?int $sort = 2;

    public function getHeading(): string
    {
        return __('filament.client_portal.projects_progress');
    }

    protected function getData(): array
    {
        $clientId = auth('client')->id();

        $projects = Project::where('client_id', $clientId)->get();

        $labels = $projects->pluck('name')->toArray();
        $completion = $projects->pluck('completion_percentage')->toArray();

        return [
            'datasets' => [
                [
                    'label' => __('filament.columns.completion_percentage'),
                    'data' => $completion,
                    'backgroundColor' => array_map(fn ($p) => $p >= 100 ? 'rgba(34,197,94,0.6)' : ($p >= 50 ? 'rgba(234,179,8,0.6)' : 'rgba(239,68,68,0.6)'), $completion),
                    'borderColor' => array_map(fn ($p) => $p >= 100 ? 'rgba(34,197,94,1)' : ($p >= 50 ? 'rgba(234,179,8,1)' : 'rgba(239,68,68,1)'), $completion),
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
}
