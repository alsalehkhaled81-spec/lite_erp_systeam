<?php

namespace App\Filament\Employee\Pages;

use App\Models\Task;
use Filament\Pages\Page;
use Illuminate\Support\Collection;

class MyTasksKanban extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-view-columns';
    protected static ?string $navigationLabel;
    protected static ?string $slug = 'my-tasks-kanban';
    protected static string $view = 'filament.pages.my-tasks-kanban';

    public static function getNavigationLabel(): string
    {
        return __('filament.nav_kanban');
    }

    protected function getStatuses(): array
    {
        return [
            ['id' => 'todo', 'title' => __('filament.status_todo')],
            ['id' => 'in_progress', 'title' => __('filament.status_in_progress')],
            ['id' => 'review', 'title' => __('filament.status_review')],
            ['id' => 'done', 'title' => __('filament.status_done')],
        ];
    }

    protected function getRecords(): Collection
    {
        return Task::with(['project', 'employee.user'])
            ->whereHas('employee', fn ($q) => $q->where('user_id', auth()->id()))
            ->get();
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