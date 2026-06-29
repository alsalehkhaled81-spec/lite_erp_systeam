<?php

namespace App\Filament\Hr\Resources\VacancyResource\Pages;

use App\Filament\Hr\Resources\VacancyResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditVacancy extends EditRecord
{
    protected static string $resource = VacancyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
