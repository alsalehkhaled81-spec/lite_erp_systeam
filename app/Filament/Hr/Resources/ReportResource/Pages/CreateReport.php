<?php

namespace App\Filament\Hr\Resources\ReportResource\Pages;

use App\Filament\Hr\Resources\ReportResource;
use App\Models\Employee;
use Filament\Resources\Pages\CreateRecord;

class CreateReport extends CreateRecord
{
    protected static string $resource = ReportResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (empty($data['sender_id'])) {
            $employee = Employee::where('user_id', auth()->id())->first();
            $data['sender_id'] = $employee?->id;
        }
        $data['status'] = $data['status'] ?? 'unread';

        return $data;
    }

    protected function afterCreate(): void
    {
        $this->record->notifyReceiver();
        $this->record->notifySender();
    }
}
