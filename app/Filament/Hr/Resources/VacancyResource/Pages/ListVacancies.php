<?php

namespace App\Filament\Hr\Resources\VacancyResource\Pages;

use App\Filament\Hr\Resources\VacancyResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListVacancies extends ListRecords
{
    protected static string $resource = VacancyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
