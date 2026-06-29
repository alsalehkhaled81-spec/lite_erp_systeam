<?php

namespace App\Filament\Hr\Widgets;

use App\Models\Employee;
use App\Models\Vacancy;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

class HrStatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        // 1. Avg Tenure
        $activeEmployees = Employee::where('status', 'active')->get();
        $totalDays = 0;
        foreach ($activeEmployees as $emp) {
            if ($emp->hire_date) {
                $totalDays += Carbon::parse($emp->hire_date)->diffInDays(now());
            }
        }
        $activeCount = $activeEmployees->count();
        $avgYears = $activeCount > 0 ? ($totalDays / $activeCount / 365.25) : 0;
        
        // 2. Turnover Rate
        $departedCount = Employee::whereIn('status', ['terminated', 'resigned'])->count();
        $totalEmployees = Employee::count();
        $turnoverRate = $totalEmployees > 0 ? ($departedCount / $totalEmployees) * 100 : 0;
        
        // 3. Open Capacity
        $totalPositions = Vacancy::where('status', 'active')->sum('positions_count');
        $filledPositions = $activeCount; 
        $limit = $filledPositions + $totalPositions;

        return [
            Stat::make(__('متوسط فترة الخدمة'), number_format($avgYears, 1) . ' ' . __('سنوات'))
                ->description("{$activeCount} " . __('موظف على رأس العمل'))
                ->descriptionIcon('heroicon-m-clock')
                ->color('primary')
                ->extraAttributes(['class' => 'cursor-pointer transition-all duration-300']),

            Stat::make(__('معدل الدوران/الاستقالة'), number_format($turnoverRate, 1) . '%')
                ->description("{$departedCount} " . __('غادروا من أصل') . " {$totalEmployees}")
                ->descriptionIcon('heroicon-m-arrow-right-on-rectangle')
                ->color('warning')
                ->extraAttributes(['class' => 'cursor-pointer transition-all duration-300']),

            Stat::make(__('الشواغر المتاحة (Open Capacity)'), $totalPositions)
                ->description(__('السعة القصوى:') . " {$limit}, " . __('المشغول:') . " {$filledPositions}")
                ->descriptionIcon('heroicon-m-user-plus')
                ->color('success')
                ->extraAttributes(['class' => 'cursor-pointer transition-all duration-300']),
        ];
    }
}
