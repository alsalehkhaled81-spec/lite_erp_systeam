<?php

namespace App\Filament\Hr\Resources\AttendanceResource\Pages;

use App\Filament\Hr\Resources\AttendanceResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAttendance extends EditRecord
{
    protected static string $resource = AttendanceResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()];
    }
}
