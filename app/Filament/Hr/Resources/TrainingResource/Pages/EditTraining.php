<?php

namespace App\Filament\Hr\Resources\TrainingResource\Pages;

use App\Filament\Hr\Resources\TrainingResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTraining extends EditRecord
{
    protected static string $resource = TrainingResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()];
    }
}
