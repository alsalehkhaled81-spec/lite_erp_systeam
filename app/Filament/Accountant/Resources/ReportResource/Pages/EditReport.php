<?php

namespace App\Filament\Accountant\Resources\ReportResource\Pages;

use App\Filament\Accountant\Resources\ReportResource;
use Filament\Resources\Pages\EditRecord;

class EditReport extends EditRecord
{
    protected static string $resource = ReportResource::class;

    protected function afterSave(): void
    {
        if (
            $this->record->feedback &&
            $this->record->receiver_id === ReportResource::personalEmployeeId() &&
            $this->record->status !== 'replied'
        ) {
            $this->record->update(['status' => 'replied']);
            $this->record->refresh();
            $this->record->notifyReplied();
        }
    }
}
