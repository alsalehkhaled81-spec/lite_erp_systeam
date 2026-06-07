<?php

namespace App\Filament\Employee\Widgets;

use App\Models\Task;
use Filament\Widgets\Widget;

class EmployeeProgressRing extends Widget
{
    protected static string $view = 'filament.widgets.employee-progress-ring';
    protected static ?int $sort = 2;
    protected int | string | array $columnSpan = 1;

    public function getViewData(): array
    {
        $employeeId = auth()->user()->employee->id ?? null;
        if (!$employeeId) {
            return ['percentage' => 0, 'total' => 0, 'completed' => 0, 'inProgress' => 0, 'pending' => 0];
        }

        $total = Task::where('employee_id', $employeeId)->count();
        $completed = Task::where('employee_id', $employeeId)->where('status', 'done')->count();
        $inProgress = Task::where('employee_id', $employeeId)->where('status', 'in_progress')->count();
        $pending = Task::where('employee_id', $employeeId)->where('status', 'todo')->count();
        $percentage = $total > 0 ? round(($completed / $total) * 100) : 0;

        return [
            'percentage' => $percentage,
            'total' => $total,
            'completed' => $completed,
            'inProgress' => $inProgress,
            'pending' => $pending,
        ];
    }
}
