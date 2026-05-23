<?php

namespace App\Filament\Employee\Resources\LeaveResource\Pages;

use App\Filament\Employee\Resources\LeaveResource;
use App\Models\Employee;
use Filament\Resources\Pages\CreateRecord;

class CreateLeave extends CreateRecord
{
    protected static string $resource = LeaveResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $employee = Employee::where('user_id', auth()->id())->first();
        $data['employee_id'] = $employee?->id;
        $data['status'] = 'pending';
        return $data;
    }
}
