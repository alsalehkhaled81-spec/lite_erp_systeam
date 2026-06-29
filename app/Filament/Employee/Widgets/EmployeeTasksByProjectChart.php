<?php

namespace App\Filament\Employee\Widgets;

use App\Models\Task;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class EmployeeTasksByProjectChart extends ChartWidget
{
    protected static ?string $heading = 'توزيع المهام على المشاريع';
    protected static ?int $sort = 4;

    protected function getData(): array
    {
        $employeeId = auth()->user()->employee->id ?? null;
        
        if (!$employeeId) {
            return [
                'datasets' => [],
                'labels' => [],
            ];
        }

        $tasks = Task::where('employee_id', $employeeId)
            ->whereNotNull('project_id')
            ->select('project_id', DB::raw('count(*) as total'))
            ->groupBy('project_id')
            ->with('project')
            ->get();

        $labels = [];
        $data = [];
        $colors = ['#3b82f6', '#8b5cf6', '#f59e0b', '#10b981', '#ec4899', '#6366f1'];

        // Repeat colors if needed
        while(count($colors) < count($tasks)) {
            $colors = array_merge($colors, $colors);
        }

        foreach ($tasks as $index => $task) {
            $labels[] = $task->project ? (strlen($task->project->name) > 15 ? substr($task->project->name, 0, 15) . '...' : $task->project->name) : 'مشروع غير محدد';
            $data[] = $task->total;
        }

        return [
            'datasets' => [
                [
                    'label' => 'عدد المهام',
                    'data' => $data,
                    'backgroundColor' => array_slice($colors, 0, count($data)),
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'pie';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'position' => 'bottom',
                ],
            ],
            'scales' => [
                'x' => ['display' => false],
                'y' => ['display' => false],
            ],
        ];
    }
}
