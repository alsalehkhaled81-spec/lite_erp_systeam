<?php

namespace App\Filament\Client\Widgets;

use App\Models\Task;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class ClientTasksStatusChart extends ChartWidget
{
    protected static ?string $heading = null;

    protected static ?int $sort = 2;

    public function getHeading(): string
    {
        return 'حالة المهام (التوزيع العام)';
    }

    protected function getData(): array
    {
        $clientId = auth('client')->id();

        $statuses = Task::whereHas('project', function ($q) use ($clientId) {
            $q->where('client_id', $clientId);
        })
        ->select('status', DB::raw('count(*) as total'))
        ->groupBy('status')
        ->pluck('total', 'status')
        ->toArray();

        $labels = [];
        $data = [];
        $colors = [];

        $statusColors = [
            'pending' => 'rgba(156, 163, 175, 0.8)',
            'in_progress' => 'rgba(234, 179, 8, 0.8)',
            'completed' => 'rgba(34, 197, 94, 0.8)',
            'under_review' => 'rgba(59, 130, 246, 0.8)',
            'on_hold' => 'rgba(249, 115, 22, 0.8)',
            'cancelled' => 'rgba(239, 68, 68, 0.8)',
        ];

        foreach ($statuses as $status => $count) {
            $labels[] = __("filament.status.{$status}");
            $data[] = $count;
            $colors[] = $statusColors[$status] ?? 'rgba(156, 163, 175, 0.8)';
        }

        if (empty($data)) {
            $labels[] = 'لا توجد مهام';
            $data[] = 0;
            $colors[] = 'rgba(156, 163, 175, 0.2)';
        }

        return [
            'datasets' => [
                [
                    'data' => $data,
                    'backgroundColor' => $colors,
                    'borderColor' => 'transparent',
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
