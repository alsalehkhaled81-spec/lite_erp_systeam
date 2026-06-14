<?php

namespace App\Filament\Hr\Pages;

use App\Models\Leave;
use App\Models\Employee;
use Filament\Pages\Page;

class TeamCalendar extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-calendar';
    protected static string $view = 'filament.pages.team-calendar';
    protected static ?string $navigationGroup = null;
    protected static ?string $slug = 'team-calendar';
    public static function getNavigationGroup(): ?string
    {
        return __('filament.group.leaves');
    }


    public static function getNavigationLabel(): string
    {
        return __('filament.nav.team_calendar');
    }

    public function getViewData(): array
    {
        $approvedLeaves = Leave::with('employee.user')
            ->whereIn('status', ['approved_by_head', 'approved_by_hr'])
            ->orderBy('start_date')
            ->get()
            ->map(function ($leave) {
                $typeColors = [
                    'Sick' => '#ef4444',
                    'Annual' => '#3b82f6',
                    'Emergency' => '#f59e0b',
                ];
                return [
                    'title' => ($leave->employee?->user?->name ?? '') . ' - ' . __('filament.leave_type.' . $leave->type),
                    'start' => $leave->start_date->toDateString(),
                    'end' => $leave->end_date->addDay()->toDateString(),
                    'color' => $typeColors[$leave->type] ?? '#6b7280',
                    'type' => $leave->type,
                ];
            });

        return [
            'events' => $approvedLeaves->toJson(),
        ];
    }
}
