<?php

namespace App\Filament\Employee\Widgets;

use App\Models\Task;
use App\Models\Leave;
use Filament\Widgets\Widget;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class EmployeeCalendarWidget extends Widget
{
    protected static string $view = 'filament.widgets.employee-calendar';

    protected static ?int $sort = 3;

    protected int | string | array $columnSpan = 'full';

    public function getViewData(): array
    {
        $employee = auth()->user()->employee;

        if (!$employee) {
            return [
                'events' => collect(),
                'month' => now()->month,
                'year' => now()->year,
                'daysInMonth' => now()->daysInMonth,
                'firstDayOfWeek' => now()->copy()->startOfMonth()->dayOfWeekIso,
                'today' => now()->day,
            ];
        }

        $now = now();
        $startOfMonth = $now->copy()->startOfMonth()->startOfDay();
        $endOfNextMonth = $now->copy()->addMonth()->endOfMonth()->endOfDay();

        $tasks = Task::where('employee_id', $employee->id)
            ->whereNotNull('due_date')
            ->whereBetween('due_date', [$startOfMonth, $endOfNextMonth])
            ->get();

        $leaves = Leave::where('employee_id', $employee->id)
            ->where('status', 'approved')
            ->where(function ($query) use ($startOfMonth, $endOfNextMonth) {
                $query->whereBetween('start_date', [$startOfMonth, $endOfNextMonth])
                    ->orWhereBetween('end_date', [$startOfMonth, $endOfNextMonth])
                    ->orWhere(function ($q) use ($startOfMonth, $endOfNextMonth) {
                        $q->where('start_date', '<=', $startOfMonth)
                            ->where('end_date', '>=', $endOfNextMonth);
                    });
            })
            ->get();

        $events = collect();

        $statusColors = [
            'todo' => 'gray',
            'in_progress' => 'yellow',
            'review' => 'blue',
            'done' => 'green',
        ];

        foreach ($tasks as $task) {
            $events->push([
                'title' => $task->title,
                'date' => Carbon::parse($task->due_date)->format('Y-m-d'),
                'type' => 'task',
                'color' => $statusColors[$task->status] ?? 'gray',
            ]);
        }

        foreach ($leaves as $leave) {
            $start = Carbon::parse($leave->start_date);
            $end = Carbon::parse($leave->end_date);

            for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
                $events->push([
                    'title' => $leave->type,
                    'date' => $date->format('Y-m-d'),
                    'type' => 'leave',
                    'color' => 'purple',
                ]);
            }
        }

        return [
            'events' => $events->groupBy('date'),
            'month' => $now->month,
            'year' => $now->year,
            'daysInMonth' => $now->daysInMonth,
            'firstDayOfWeek' => $now->copy()->startOfMonth()->dayOfWeekIso,
            'today' => $now->format('Y-m-d'),
        ];
    }
}