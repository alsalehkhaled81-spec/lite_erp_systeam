<?php

namespace App\Filament\Resources\ReportResource\Pages;

use App\Filament\Resources\ReportResource;
use App\Models\Employee;
use Filament\Resources\Pages\EditRecord;

class EditReport extends EditRecord
{
    protected static string $resource = ReportResource::class;

    protected function afterSave(): void
    {
        $currentEmployeeId = Employee::where('user_id', auth()->id())->value('id');

        if (
            $this->record->feedback &&
            $this->record->receiver_id === $currentEmployeeId &&
            $this->record->status !== 'replied'
        ) {
            $this->record->update(['status' => 'replied']);
            $this->record->refresh();
            $this->record->notifyReplied();
        }
    }
}
