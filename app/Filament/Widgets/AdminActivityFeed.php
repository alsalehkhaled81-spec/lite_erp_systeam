<?php

namespace App\Filament\Widgets;

use App\Models\Employee;
use App\Models\Invoice;
use App\Models\Leave;
use App\Models\Project;
use App\Models\Task;
use Filament\Widgets\Widget;

class AdminActivityFeed extends Widget
{
    protected static string $view = 'filament.widgets.admin-activity-feed';
    protected static ?int $sort = 4;
    protected int | string | array $columnSpan = 'full';

    public function getViewData(): array
    {
        $activities = collect();

        $recentProjects = Project::with('client')->latest()->take(5)->get();
        foreach ($recentProjects as $project) {
            $activities->push([
                'type' => 'project',
                'icon' => 'heroicon-o-briefcase',
                'color' => 'text-blue-500 bg-blue-100 dark:bg-blue-900/30',
                'title' => $project->name,
                'description' => __('filament.widgets.activity_project_created'),
                'time' => $project->created_at,
            ]);
        }

        $recentTasks = Task::with(['project', 'employee.user'])->latest()->take(5)->get();
        foreach ($recentTasks as $task) {
            $statusColors = [
                'todo' => 'text-gray-500 bg-gray-100 dark:bg-gray-800',
                'in_progress' => 'text-yellow-500 bg-yellow-100 dark:bg-yellow-900/30',
                'review' => 'text-blue-500 bg-blue-100 dark:bg-blue-900/30',
                'done' => 'text-green-500 bg-green-100 dark:bg-green-900/30',
            ];
            $activities->push([
                'type' => 'task',
                'icon' => 'heroicon-o-clipboard-document-check',
                'color' => $statusColors[$task->status] ?? 'text-gray-500 bg-gray-100',
                'title' => $task->title,
                'description' => __('filament.widgets.activity_task_' . $task->status, ['project' => $task->project?->name ?? '-']),
                'time' => $task->updated_at,
            ]);
        }

        $recentInvoices = Invoice::with('client')->latest()->take(5)->get();
        foreach ($recentInvoices as $invoice) {
            $isPaid = $invoice->status === 'paid';
            $activities->push([
                'type' => 'invoice',
                'icon' => 'heroicon-o-document-currency-dollar',
                'color' => $isPaid ? 'text-green-500 bg-green-100 dark:bg-green-900/30' : 'text-red-500 bg-red-100 dark:bg-red-900/30',
                'title' => $invoice->invoice_number,
                'description' => $isPaid
                    ? __('filament.widgets.activity_invoice_paid', ['amount' => number_format($invoice->amount, 2)])
                    : __('filament.widgets.activity_invoice_unpaid', ['amount' => number_format($invoice->amount, 2)]),
                'time' => $invoice->updated_at,
            ]);
        }

        $recentLeaves = Leave::with('employee.user')->latest()->take(5)->get();
        foreach ($recentLeaves as $leave) {
            $statusColors = [
                'pending' => 'text-yellow-500 bg-yellow-100 dark:bg-yellow-900/30',
                'approved' => 'text-green-500 bg-green-100 dark:bg-green-900/30',
                'rejected' => 'text-red-500 bg-red-100 dark:bg-red-900/30',
            ];
            $activities->push([
                'type' => 'leave',
                'icon' => 'heroicon-o-calendar-days',
                'color' => $statusColors[$leave->status] ?? 'text-gray-500 bg-gray-100',
                'title' => $leave->employee?->user?->name ?? '-',
                'description' => __('filament.widgets.activity_leave_' . $leave->status),
                'time' => $leave->updated_at,
            ]);
        }

        $recentEmployees = Employee::with('user', 'department')->latest()->take(5)->get();
        foreach ($recentEmployees as $emp) {
            $activities->push([
                'type' => 'employee',
                'icon' => 'heroicon-o-users',
                'color' => 'text-purple-500 bg-purple-100 dark:bg-purple-900/30',
                'title' => $emp->user?->name ?? '-',
                'description' => __('filament.widgets.activity_employee_joined', ['department' => $emp->department?->name ?? '-']),
                'time' => $emp->created_at,
            ]);
        }

        return [
            'activities' => $activities->sortByDesc('time')->take(15),
        ];
    }
}
