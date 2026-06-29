<?php

namespace App\Filament\Pm\Widgets;

use App\Models\Project;
use Filament\Widgets\ChartWidget;

class ProjectHealthChart extends ChartWidget
{
    protected static ?string $heading = 'صحة المشاريع (Project Health)';
    protected static ?int $sort = 3;

    protected function getData(): array
    {
        // Get active projects or a limited number
        $projects = Project::where('status', '!=', 'completed')->limit(10)->get();

        $labels = [];
        $budgetUsed = [];
        $timelineElapsed = [];

        foreach ($projects as $project) {
            $labels[] = strlen($project->name) > 20 ? substr($project->name, 0, 20) . '...' : $project->name;

            // Budget Used %
            $budget = $project->budget > 0 ? $project->budget : 1;
            $expenses = $project->expenses()->sum('amount');
            $budgetPercentage = min(100, round(($expenses / $budget) * 100));
            $budgetUsed[] = $budgetPercentage;

            // Timeline Elapsed %
            if ($project->start_date && $project->end_date) {
                $start = \Illuminate\Support\Carbon::parse($project->start_date);
                $end = \Illuminate\Support\Carbon::parse($project->end_date);
                $now = now();
                
                $totalDays = $end->diffInDays($start);
                $elapsedDays = $now->diffInDays($start, false); // false for negative if future
                
                if ($elapsedDays < 0) {
                    $timelinePercentage = 0;
                } elseif ($elapsedDays > $totalDays || $totalDays == 0) {
                    $timelinePercentage = 100;
                } else {
                    $timelinePercentage = round(($elapsedDays / $totalDays) * 100);
                }
            } else {
                $timelinePercentage = 0;
            }
            $timelineElapsed[] = $timelinePercentage;
        }

        return [
            'datasets' => [
                [
                    'label' => 'نسبة استهلاك الميزانية (Budget Used %)',
                    'data' => $budgetUsed,
                    'backgroundColor' => '#3b82f6', // Blue color
                ],
                [
                    'label' => 'النسبة المنقضية من الوقت (Timeline Elapsed %)',
                    'data' => $timelineElapsed,
                    'backgroundColor' => '#f59e0b', // Amber/Orange color
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
