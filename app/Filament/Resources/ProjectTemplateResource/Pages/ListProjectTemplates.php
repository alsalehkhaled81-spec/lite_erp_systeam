<?php

namespace App\Filament\Resources\ProjectTemplateResource\Pages;

use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\ProjectTemplateResource;
use Filament\Actions;

class ListProjectTemplates extends ListRecords
{
    protected static string $resource = ProjectTemplateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
