<?php

namespace App\Filament\Pm\Pages;

use App\Models\Task;
use App\Models\Project;
use App\Models\Employee;
use Filament\Actions\Action;
use Filament\Pages\Page;
use Illuminate\Support\Collection;

class TasksKanbanBoard extends Page
{
    protected static ?string $model = Task::class;
    protected static ?string $navigationIcon = 'heroicon-o-view-columns';
    protected static string $view = 'filament.pages.tasks-kanban';
    protected static ?string $slug = 'tasks-kanban';

    public ?int $filterProjectId = null;
    public ?int $filterEmployeeId = null;

    public static function getNavigationLabel(): string
    {
        return __('filament.nav.kanban_board');
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

        return $query->orderBy('priority', 'desc')->get();
    }

    public function updateTaskStatus(int $taskId, string $newStatus): void
    {
        $task = Task::find($taskId);
        if ($task && in_array($newStatus, ['todo', 'in_progress', 'review', 'done'])) {
            $task->update(['status' => $newStatus]);
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
