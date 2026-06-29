<?php

namespace App\Filament\Accountant\Resources\ReportResource\Pages;

use App\Filament\Accountant\Resources\ReportResource;
use App\Models\Employee;
use Filament\Resources\Pages\CreateRecord;

class CreateReport extends CreateRecord
{
    protected static string $resource = ReportResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $employee = Employee::where('user_id', auth()->id())->first();
        $data['sender_id'] = $employee?->id;
        $data['status'] = 'unread';

        return $data;
    }

    protected function afterCreate(): void
    {
        $this->record->notifyReceiver();
        $this->record->notifySender();
    }
}
