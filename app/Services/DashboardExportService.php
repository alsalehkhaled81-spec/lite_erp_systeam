<?php

namespace App\Services;

use App\Models\Employee;
use App\Models\Project;
use App\Models\Task;
use App\Models\Invoice;
use App\Models\Expense;
use Barryvdh\DomPDF\Facade\Pdf;

class DashboardExportService
{
    public function generatePdf(): mixed
    {
        $totalEmployees = Employee::count();
        $activeEmployees = Employee::where('status', 'active')->count();
        $totalProjects = Project::count();
        $activeProjects = Project::where('status', 'in_progress')->count();
        $totalTasks = Task::count();
        $doneTasks = Task::where('status', 'done')->count();
        $totalRevenue = Invoice::where('status', 'paid')->sum('amount');
        $totalExpenses = Expense::sum('amount');
        $netProfit = $totalRevenue - $totalExpenses;
        $taskRate = $totalTasks > 0 ? round(($doneTasks / $totalTasks) * 100) : 0;

        $kpis = [
            [
                'label' => __('filament.widgets.total_employees'),
                'value' => $totalEmployees,
            ],
            [
                'label' => __('filament.widgets.total_projects'),
                'value' => $totalProjects,
            ],
            [
                'label' => __('filament.widgets.total_tasks'),
                'value' => $totalTasks,
                'trend' => $taskRate,
            ],
            [
                'label' => __('filament.widgets.net_profit'),
                'value' => '$' . number_format($netProfit, 2),
                'trend' => $netProfit >= 0 ? 5 : -5,
            ],
        ];

        $monthlyData = [];
        for ($i = 11; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $rev = (float) Invoice::where('status', 'paid')
                ->whereYear('issue_date', $month->year)
                ->whereMonth('issue_date', $month->month)
                ->sum('amount');
            $exp = (float) Expense::whereYear('expense_date', $month->year)
                ->whereMonth('expense_date', $month->month)
                ->sum('amount');

            $monthlyData[] = [
                'month' => $month->translatedFormat('M Y'),
                'revenue' => $rev,
                'expense' => $exp,
                'net' => $rev - $exp,
            ];
        }

        $activities = collect(Task::latest()->take(15)->get())->map(function ($task) {
            return [
                'color' => 'badge-info',
                'icon' => 'T',
                'description' => $task->title,
                'time' => $task->created_at->diffForHumans(),
            ];
        });

        $employeeStats = Employee::with('user')->withCount(['tasks', 'tasks as done_tasks' => function ($query) {
            $query->where('status', 'done');
        }])->take(10)->get()->map(function ($emp) {
            return [
                'name' => $emp->user->name,
                'tasks' => $emp->tasks_count,
                'rate' => $emp->tasks_count > 0 ? round(($emp->done_tasks / $emp->tasks_count) * 100) : 0,
            ];
        });

        $data = [
            'company' => config('app.name', 'ERP System'),
            'title' => __('filament.actions.export_dashboard_pdf'),
            'generatedAt' => now()->translatedFormat('Y-m-d H:i'),
            'kpis' => $kpis,
            'monthlyData' => $monthlyData,
            'activities' => $activities,
            'employeeStats' => $employeeStats,
        ];

        $pdf = Pdf::loadView('pdf.dashboard-report', $data);
        $pdf->setPaper('a4', 'landscape');

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, 'dashboard_report_' . now()->format('Y_m_d_His') . '.pdf');
    }
}
