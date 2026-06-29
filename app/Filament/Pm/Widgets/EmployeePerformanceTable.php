<?php

namespace App\Filament\Pm\Widgets;

use App\Models\Employee;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Database\Eloquent\Builder;

class EmployeePerformanceTable extends BaseWidget
{
    use InteractsWithPageFilters;

    protected static ?int $sort = 4;
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        $startDate = $this->filters['startDate'] ?? null;
        $endDate = $this->filters['endDate'] ?? null;

        return $table
            ->query(
                Employee::query()
                    ->whereHas('tasks')
                    ->withCount([
                        'tasks as total_tasks_count' => function (Builder $query) use ($startDate, $endDate) {
                            if ($startDate) $query->whereDate('created_at', '>=', $startDate);
                            if ($endDate) $query->whereDate('created_at', '<=', $endDate);
                        },
                        'tasks as completed_tasks_count' => function (Builder $query) use ($startDate, $endDate) {
                            $query->where('status', 'done');
                            if ($startDate) $query->whereDate('created_at', '>=', $startDate);
                            if ($endDate) $query->whereDate('created_at', '<=', $endDate);
                        },
                        'tasks as overdue_tasks_count' => function (Builder $query) use ($startDate, $endDate) {
                            $query->where('status', '!=', 'done')->whereDate('due_date', '<', now());
                            if ($startDate) $query->whereDate('due_date', '>=', $startDate);
                            if ($endDate) $query->whereDate('due_date', '<=', $endDate);
                        },
                    ])
            )
            ->heading(__('filament.widgets.employee_performance') ?? 'أداء الموظفين')
            ->description(__('filament.widgets.employee_performance_desc') ?? 'إحصائيات إنجاز المهام للموظفين في قسمك ضمن الفترة المحددة')
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label(__('filament.widgets.employee_name') ?? 'اسم الموظف')
                    ->searchable()
                    ->sortable()
                    ->description(fn (Employee $record): string => $record->job_title ?? ''),
                
                Tables\Columns\TextColumn::make('total_tasks_count')
                    ->label(__('filament.widgets.assigned_tasks') ?? 'المهام المسندة')
                    ->sortable()
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('completed_tasks_count')
                    ->label(__('filament.widgets.completed_tasks') ?? 'المهام المنجزة')
                    ->sortable()
                    ->badge()
                    ->color('success'),

                Tables\Columns\TextColumn::make('overdue_tasks_count')
                    ->label(__('filament.widgets.overdue_tasks_table') ?? 'المهام المتأخرة')
                    ->sortable()
                    ->badge()
                    ->color(fn (string $state): string => $state > 0 ? 'danger' : 'gray'),
                
                Tables\Columns\TextColumn::make('completion_rate')
                    ->label(__('filament.widgets.completion_rate') ?? 'نسبة الإنجاز')
                    ->state(function (Employee $record): string {
                        $total = $record->total_tasks_count;
                        if ($total === 0) return 'N/A';
                        $completed = $record->completed_tasks_count;
                        $rate = round(($completed / $total) * 100);
                        return "{$rate}%";
                    })
                    ->badge()
                    ->color(function (string $state): string {
                        if ($state === 'N/A') return 'gray';
                        $rate = (int) str_replace('%', '', $state);
                        if ($rate >= 80) return 'success';
                        if ($rate >= 50) return 'warning';
                        return 'danger';
                    }),
            ])
            ->paginated(false);
    }
}
