<?php

namespace App\Filament\Widgets;

use App\Models\Project;
use Filament\Widgets\ChartWidget;

class AdminProjectsChart extends ChartWidget
{
    protected static ?string $heading = 'حالة المشاريع';
    protected static ?int $sort = 2;

    protected function getData(): array
    {
        return [
            'datasets' => [[
                    'label' => 'المشاريع',
                    'data' =>[
                        Project::where('status', 'pending')->count(),
                        Project::where('status', 'in_progress')->count(),
                        Project::where('status', 'completed')->count(),
                        Project::where('status', 'canceled')->count(),
                    ],
                    'backgroundColor' =>['#9ca3af', '#f59e0b', '#10b981', '#ef4444'],
                ],
            ],
            'labels' =>['قيد الانتظار', 'قيد التنفيذ', 'مكتملة', 'ملغاة'],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut'; // مخطط دائري
    }
}
