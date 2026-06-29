<?php

namespace App\Filament\Hr\Widgets;

use App\Models\Employee;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class DepartmentLeaveLoadChart extends ChartWidget
{
    protected static ?string $heading = 'ضغط الإجازات حسب القسم (Department Leave Load)';
    protected static ?int $sort = 3;

    protected function getData(): array
    {
        $data = Employee::with('department')
            ->select('department_id', DB::raw('SUM(used_leave_days) as total_leaves'))
            ->groupBy('department_id')
            ->get();

        $labels = [];
        $values = [];

        foreach ($data as $item) {
            $labels[] = $item->department ? $item->department->name : 'بدون قسم';
            $values[] = (int) $item->total_leaves;
        }

        return [
            'datasets' => [
                [
                    'label' => 'أيام الإجازات المستهلكة',
                    'data' => $values,
                    'backgroundColor' => '#8b5cf6', // Purple color
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
