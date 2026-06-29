<?php

namespace App\Filament\Employee\Widgets;

use Filament\Widgets\ChartWidget;

class EmployeeLeaveBalanceChart extends ChartWidget
{
    protected static ?string $heading = 'رصيد الإجازات السنوي';
    protected static ?int $sort = 3;

    protected function getData(): array
    {
        $employee = auth()->user()->employee;
        
        if (!$employee) {
            return [
                'datasets' => [],
                'labels' => [],
            ];
        }

        $used = $employee->used_leave_days;
        $remaining = $employee->remaining_leave_balance;

        return [
            'datasets' => [
                [
                    'label' => 'الأيام',
                    'data' => [$used, $remaining],
                    'backgroundColor' => ['#f43f5e', '#10b981'], // Rose for used, Emerald for remaining
                    'hoverOffset' => 4,
                ],
            ],
            'labels' => ['رصيد مستهلك', 'رصيد متبقي'],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getOptions(): array
    {
        return [
            'cutout' => '70%',
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
