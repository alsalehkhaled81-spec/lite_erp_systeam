<?php

namespace App\Filament\Hr\Resources\AttendanceResource\Pages;

use App\Filament\Hr\Resources\AttendanceResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAttendances extends ListRecords
{
    protected static string $resource = AttendanceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('daily_attendance')
                ->label(__('filament.attendance.daily_attendance'))
                ->icon('heroicon-o-calendar-days')
                ->color('info')
                ->url(fn () => DailyAttendance::getUrl()),
            Actions\CreateAction::make(),
        ];
    }
}
