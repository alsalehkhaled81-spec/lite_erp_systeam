<?php

namespace App\Filament\Pm\Pages;

use App\Models\Task;
use Filament\Pages\Page;
use Illuminate\Support\Collection;

class TasksKanbanBoard extends Page
{
    protected static ?string $model = Task::class;
    protected static ?string $navigationIcon = 'heroicon-o-view-columns';
    protected static string $view = 'filament.pages.tasks-kanban';

    public static function getNavigationLabel(): string
    {
        return __('filament.nav.kanban_board');
    }

    protected static ?string $slug = 'tasks-kanban';

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
        return Task::with(['project', 'employee.user'])->get();
    }

    public function getViewData(): array
    {
        $statuses = collect($this->getStatuses())->map(function ($status) {
            $status['records'] = $this->getRecords()->where('status', $status['id']);
            return $status;
        });

        return [
            'statuses' => $statuses,
        ];
    }
}
