<?php

namespace App\Filament\Pm\Pages;

use App\Models\Task;
use App\Models\Project;
use Filament\Pages\Page;

class GanttChart extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';
    protected static string $view = 'filament.pages.gantt-chart';
    protected static ?string $slug = 'gantt-chart';

    public static function getNavigationLabel(): string
    {
        return __('filament.nav.gantt_chart');
    }

    public static function getNavigationGroup(): string
    {
        return __('filament.group.projects_tasks');
    }

    public function getViewData(): array
    {
        $statusColors = [
            'todo' => '#6b7280',
            'in_progress' => '#f59e0b',
            'review' => '#3b82f6',
            'done' => '#10b981',
        ];

        $tasks = Task::with(['project', 'employee.user'])->get()->map(function ($task) use ($statusColors) {
            $start = $task->start_date
                ? \Carbon\Carbon::parse($task->start_date)
                : ($task->created_at ?? now());

            $end = $task->due_date
                ? \Carbon\Carbon::parse($task->due_date)
                : $start->copy()->addDays(7);

            // frappe-gantt requires the end date to be strictly after the start
            // date, otherwise the whole chart fails to render.
            if ($end <= $start) {
                $end = $start->copy()->addDay();
            }

            return [
                'id' => (string) $task->id,
                'name' => $task->title,
                'start' => $start->toDateString(),
                'end' => $end->toDateString(),
                'progress' => $task->status === 'done' ? 100 : ($task->status === 'in_progress' ? 50 : ($task->status === 'review' ? 80 : 0)),
                'dependencies' => '',
                'project' => $task->project?->name ?? '',
                'project_id' => $task->project_id,
                'employee' => $task->employee?->user?->name ?? '',
                'status' => $task->status,
                'color' => $statusColors[$task->status] ?? '#6b7280',
                'url' => \App\Filament\Pm\Resources\TaskResource::getUrl('edit', ['record' => $task->id]),
            ];
        })->values();

        return [
            'tasks' => $tasks,
            'projects' => Project::pluck('name', 'id'),
        ];
    }
}
