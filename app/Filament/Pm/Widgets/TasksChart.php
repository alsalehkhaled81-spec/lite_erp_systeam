<?php

namespace App\Filament\Pm\Widgets;

use App\Models\Task;
use Filament\Widgets\ChartWidget;

class TasksChart extends ChartWidget
{
    protected static ?string $heading = 'إحصائيات المهام';

    protected function getData(): array
    {
        return [
            'datasets' => [[
                    'label' => 'عدد المهام',
                    'data' =>[
                        Task::where('status', 'todo')->count(),
                        Task::where('status', 'in_progress')->count(),
                        Task::where('status', 'review')->count(),
                        Task::where('status', 'done')->count(),
                    ],
                    'backgroundColor' =>['#9ca3af', '#f59e0b', '#3b82f6', '#10b981'],
                ],
            ],
            'labels' =>['مطلوبة', 'قيد التنفيذ', 'للمراجعة', 'منتهية'],
        ];
    }

    protected function getType(): string
    {
        return 'bar'; // مخطط أعمدة
    }
}
