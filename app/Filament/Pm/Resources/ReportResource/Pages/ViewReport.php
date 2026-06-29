<?php

namespace App\Filament\Pm\Resources\ReportResource\Pages;

use App\Filament\Pm\Resources\ReportResource;
use Filament\Resources\Pages\ViewRecord;
use Filament\Actions\Action;

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
