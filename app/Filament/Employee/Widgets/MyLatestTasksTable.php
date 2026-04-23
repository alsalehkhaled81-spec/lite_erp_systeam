<?php

namespace App\Filament\Employee\Widgets;

use App\Models\Task;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class MyLatestTasksTable extends BaseWidget
{
    protected static ?string $heading = 'آخر المهام المسندة إليّ';
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        $employeeId = auth()->user()->employee->id ?? null;

        return $table
            ->query(
                Task::query()
                    ->where('employee_id', $employeeId)
                    ->where('status', '!=', 'done')
                    ->latest()
                    ->limit(5)
            )
            ->columns([
                Tables\Columns\TextColumn::make('title')->label('المهمة'),
                Tables\Columns\TextColumn::make('project.name')->label('المشروع'),
                Tables\Columns\TextColumn::make('due_date')->label('تاريخ التسليم')->date(),
                Tables\Columns\TextColumn::make('status')
                    ->label('الحالة')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'todo' => 'gray', 'in_progress' => 'warning', 'review' => 'info', 'done' => 'success', default => 'gray'
                    }),
            ])
            ->paginated(false); // إخفاء ترقيم الصفحات لأنها Dashboard
    }
}
