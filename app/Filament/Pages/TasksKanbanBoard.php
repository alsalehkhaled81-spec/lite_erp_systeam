<?php

namespace App\Filament\Pages;

use App\Models\Task;
use App\Models\Project;
use App\Models\Employee;
use Filament\Pages\Page;
use Illuminate\Support\Collection;

class TasksKanbanBoard extends Page
{
    protected static ?string $model = Task::class;
    protected static ?string $navigationIcon = 'heroicon-o-view-columns';
    protected static string $view = 'filament.pages.admin-tasks-kanban';
    protected static ?string $slug = 'tasks-kanban';

    public ?int $filterProjectId = null;
    public ?int $filterEmployeeId = null;

    public static function getNavigationLabel(): string
    {
        return __('filament.nav.kanban_board');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('filament.group.projects_tasks');
    }

    public function mount(): void
    {
        $this->filterProjectId = request()->get('project') ? (int) request()->get('project') : null;
        $this->filterEmployeeId = request()->get('employee') ? (int) request()->get('employee') : null;
    }

    protected function getStatuses(): array
    {
        return [
            ['id' => 'todo', 'title' => __('filament.status.todo')],
            ['id' => 'in_progress', 'title' => __('filament.status.in_progress')],
            ['id' => 'review', 'title' => __('filament.status.review')],
            ['id' => 'done', 'title' => __('filament.status.done')],
        ];
    }

    protected function getRecords(): Collection
    {
        $query = Task::with(['project', 'employee.user']);

        if ($this->filterProjectId) {
            $query->where('project_id', $this->filterProjectId);
        }

        if ($this->filterEmployeeId) {
            $query->where('employee_id', $this->filterEmployeeId);
        }

        return $query->orderBy('sort_order', 'asc')->orderBy('id', 'asc')->get();
    }

    public function updateTaskStatus(int $taskId, string $newStatus, int $targetIndex = 0): void
    {
        $task = Task::find($taskId);
        if (!$task || !in_array($newStatus, ['todo', 'in_progress', 'review', 'done'])) {
            return;
        }

        $oldStatus = $task->status;
        $task->update(['status' => $newStatus]);

        $siblings = Task::where('status', $newStatus)
            ->where('id', '!=', $taskId)
            ->orderBy('sort_order', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        $targetIndex = max(0, min($targetIndex, $siblings->count()));

        $ordered = $siblings->slice(0, $targetIndex)
            ->push($task)
            ->concat($siblings->slice($targetIndex));

        $ordered->values()->each(function ($t, $i) {
            if ($t->sort_order !== $i) {
                Task::where('id', $t->id)->update(['sort_order' => $i]);
            }
        });

        if ($oldStatus !== $newStatus) {
            Task::where('status', $oldStatus)
                ->where('sort_order', '>', $task->getOriginal('sort_order'))
                ->decrement('sort_order');
        }
    }

    public function applyFilters(): void
    {
        $this->getViewData();
    }

    public function getViewData(): array
    {
        $statuses = collect($this->getStatuses())->map(function ($status) {
            $status['records'] = $this->getRecords()->where('status', $status['id']);
            return $status;
        });

        return [
            'statuses' => $statuses,
            'projects' => Project::pluck('name', 'id'),
            'employees' => Employee::with('user')->get()->pluck('user.name', 'id'),
        ];
    }
}
