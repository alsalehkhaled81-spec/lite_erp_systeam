<?php

namespace App\Filament\Hr\Resources\AttendanceResource\Pages;

use App\Filament\Hr\Resources\AttendanceResource;
use App\Models\Attendance;
use Filament\Resources\Pages\CreateRecord;

class CreateAttendance extends CreateRecord
{
    protected static string $resource = AttendanceResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
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
