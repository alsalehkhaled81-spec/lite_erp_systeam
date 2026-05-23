<?php

namespace App\Filament\Employee\Resources\ReportResource\Pages;

use App\Filament\Employee\Resources\ReportResource;
use App\Models\Employee;
use App\Notifications\ReportReceivedNotification;
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
        $report = $this->record;
        $receiver = $report->receiver;
        if ($receiver?->user) {
            $senderName = $report->sender?->user?->name ?? 'موظف';
            $receiver->user->notify(new ReportReceivedNotification($report->title, $senderName));
        }
    }
}
