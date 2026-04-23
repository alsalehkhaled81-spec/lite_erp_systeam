<?php

namespace App\Filament\Hr\Widgets;

use App\Models\Employee;
use Filament\Widgets\ChartWidget;

class EmployeesChart extends ChartWidget
{
    protected static ?string $heading = 'توزيع حالة الموظفين';

    protected function getData(): array
    {
        return[
            'datasets' => [
                [
                    'label' => 'الموظفين',
                    'data' =>[
                        Employee::where('status', 'active')->count(),
                        Employee::where('status', 'on_leave')->count(),
                        Employee::where('status', 'terminated')->count(),
                    ],
                    'backgroundColor' => ['#10b981', '#f59e0b', '#ef4444'],
                ],
            ],
            'labels' =>['على رأس العمل', 'في إجازة', 'مفصول'],
        ];
    }

    protected function getType(): string
    {
        return 'pie'; // مخطط فطيرة
    }
}
