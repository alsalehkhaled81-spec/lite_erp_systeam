<?php

namespace App\Filament\Pm\Widgets;

use App\Models\Project;
use App\Models\Task;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class PmStatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return[
            Stat::make('إجمالي المشاريع', Project::count())
                ->color('primary'),
            Stat::make('مهام قيد التنفيذ', Task::where('status', 'in_progress')->count())
                ->color('warning'),
            Stat::make('مهام منتهية', Task::where('status', 'done')->count())
                ->color('success'),
        ];
    }
}
