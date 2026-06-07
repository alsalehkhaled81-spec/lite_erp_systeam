<?php

namespace App\Filament\Hr\Widgets;

use App\Models\Attendance;
use App\Models\Employee;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AttendanceSummaryWidget extends BaseWidget
{
    protected static ?int $sort = 3;

    protected function getStats(): array
    {
        $today = now()->toDateString();
        $totalActive = Employee::where('status', 'active')->count();
        $presentToday = Attendance::where('date', $today)->whereIn('status', ['present', 'late'])->count();
        $lateToday = Attendance::where('date', $today)->where('status', 'late')->count();
        $absentToday = max(0, $totalActive - $presentToday);

        $attendanceRate = $totalActive > 0 ? round(($presentToday / $totalActive) * 100, 1) : 0;

        return [
            Stat::make(__('filament.widgets.attendance_rate'), $attendanceRate . '%')
                ->description(__('filament.widgets.attendance_rate_desc'))
                ->descriptionIcon('heroicon-m-chart-bar')
                ->color($attendanceRate >= 80 ? 'success' : 'warning'),
            Stat::make(__('filament.widgets.present_today'), $presentToday)
                ->description(__('filament.widgets.out_of') . ' ' . $totalActive)
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),
            Stat::make(__('filament.widgets.late_today'), $lateToday)
                ->description(__('filament.widgets.late_today_desc'))
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),
            Stat::make(__('filament.widgets.absent_today'), $absentToday)
                ->description(__('filament.widgets.absent_today_desc'))
                ->descriptionIcon('heroicon-m-x-circle')
                ->color('danger'),
        ];
    }
}
