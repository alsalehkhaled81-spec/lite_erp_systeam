<?php

namespace App\Filament\Employee\Resources\ReportResource\Pages;

use App\Filament\Employee\Resources\ReportResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\ViewRecord;

class ViewReport extends ViewRecord
{
    protected static string $resource = ReportResource::class;

    protected function afterStateFilled(): void
    {
        $record = $this->record;

        if ($record && $record->status === 'unread' && $record->receiver_id === ReportResource::personalEmployeeId()) {
            $record->update(['status' => 'read']);
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('reply')
                ->label(__('filament.reports.reply'))
                ->url(fn () => $this->getResource()::getUrl('edit', ['record' => $this->record]))
                ->color('success')
                ->icon('heroicon-o-chat-bubble-left-right')
                ->visible(fn (): bool => $this->record->receiver_id === ReportResource::personalEmployeeId()),
        ];
    }
}
