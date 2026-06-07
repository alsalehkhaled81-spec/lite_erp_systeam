<?php

namespace App\Filament\Accountant\Resources\PayrollResource\Pages;

use App\Exports\PayrollExport;
use App\Filament\Accountant\Resources\PayrollResource;
use Filament\Actions\CreateAction;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;

class ListPayrolls extends ListRecords
{
    protected static string $resource = PayrollResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('export_excel')
                ->label(__('filament.actions.export_excel'))
                ->icon('heroicon-o-arrow-down-tray')
                ->action(fn () => (new PayrollExport())->download()),
            CreateAction::make(),
        ];
    }
}