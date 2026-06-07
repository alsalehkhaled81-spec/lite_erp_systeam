<?php

namespace App\Filament\Widgets;

use App\Models\Task;
use Filament\Widgets\Widget;
use Illuminate\Support\Collection;

class AdminEmployeeHeatmap extends Widget
{
    protected static string $view = 'filament.widgets.admin-employee-heatmap';
    protected static ?int $sort = 5;
    protected int | string | array $columnSpan = 'full';

    public function getViewData(): array
    {
        $days = collect(range(6, 0))->map(fn ($d) => now()->subDays($d)->format('Y-m-d'))->toArray();
        $dayLabels = collect(range(6, 0))->map(fn ($d) => now()->subDays($d)->format('D'))->toArray();

        $tasks = Task::selectRaw('employee_id, DATE(updated_at) as day, COUNT(*) as count')
            ->where('updated_at', '>=', now()->subDays(7))
            ->groupByRaw('employee_id, DATE(updated_at)')
            ->with('employee.user')
            ->get()
            ->groupBy('employee_id');

        $employees = \App\Models\Employee::with('user')
            ->whereHas('tasks', fn ($q) => $q->where('updated_at', '>=', now()->subDays(7)))
            ->get();

        $heatmapData = [];
        $maxCount = 1;

        foreach ($employees as $employee) {
            $employeeTasks = $tasks->get($employee->id, collect());
            $row = [];
            foreach ($days as $day) {
                $count = $employeeTasks->where('day', $day)->sum('count');
                $row[$day] = $count;
                if ($count > $maxCount) {
                    $maxCount = $count;
                }
            }
            $heatmapData[] = [
                'name' => $employee->user?->name ?? '-',
                'data' => $row,
            ];
        }

        return [
            'heatmapData' => $heatmapData,
            'days' => $dayLabels,
            'maxCount' => $maxCount,
        ];
    }

    private function getColorIntensity(int $count, int $max): string
    {
        if ($count === 0) return 'bg-gray-100 dark:bg-gray-800';
        $ratio = $count / max($max, 1);
        if ($ratio > 0.75) return 'bg-green-600 dark:bg-green-500';
        if ($ratio > 0.5) return 'bg-green-400 dark:bg-green-600';
        if ($ratio > 0.25) return 'bg-green-200 dark:bg-green-800';
        return 'bg-green-100 dark:bg-green-900';
    }
}
