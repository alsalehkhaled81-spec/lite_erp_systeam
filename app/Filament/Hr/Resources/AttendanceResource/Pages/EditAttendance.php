<?php

namespace App\Filament\Hr\Resources\AttendanceResource\Pages;

use App\Filament\Hr\Resources\AttendanceResource;
use App\Models\Attendance;
use Carbon\Carbon;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAttendance extends EditRecord
{
    protected static string $resource = AttendanceResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        if (!empty($data['check_in'])) {
            $data['check_in'] = Carbon::parse($data['check_in'])->format('H:i');
        }

        if (!empty($data['check_out'])) {
            $data['check_out'] = Carbon::parse($data['check_out'])->format('H:i');
        }

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $date = $data['date'] ?? null;
        $checkInTime = $data['check_in'] ?? null;
        $checkOutTime = $data['check_out'] ?? null;

        $data['check_in'] = Attendance::combineDateTime($date, $checkInTime);
        $data['check_out'] = Attendance::combineDateTime($date, $checkOutTime);
        $data['hours_worked'] = Attendance::calculateHoursWorked($data['check_in'], $data['check_out']);

        return $data;
    }
}
