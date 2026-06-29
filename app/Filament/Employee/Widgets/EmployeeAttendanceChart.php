<?php

namespace App\Filament\Employee\Widgets;

use App\Models\Attendance;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class EmployeeAttendanceChart extends ChartWidget
{
    protected static ?string $heading = 'ساعات العمل (آخر 7 أيام)';
    protected static ?int $sort = 5;
    protected int | string | array $columnSpan = 'full';

    protected function getData(): array
    {
        $employeeId = auth()->user()->employee->id ?? null;
        
        if (!$employeeId) {
            return [
                'datasets' => [],
                'labels' => [],
            ];
        }

        $labels = [];
        $data = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $labels[] = $date->translatedFormat('D m/d'); // Short day name

            $attendance = Attendance::where('employee_id', $employeeId)
                ->whereDate('date', $date->toDateString())
                ->first();

            $data[] = $attendance ? (float) $attendance->hours_worked : 0;
        }

        return [
            'datasets' => [
                [
                    'label' => 'ساعات العمل (Hours Worked)',
                    'data' => $data,
                    'borderColor' => '#3b82f6',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.2)',
                    'fill' => true,
                    'tension' => 0.4,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
