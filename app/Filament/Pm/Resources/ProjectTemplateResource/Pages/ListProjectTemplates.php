<?php

namespace App\Filament\Pm\Resources\ProjectTemplateResource\Pages;

use App\Filament\Pm\Resources\ProjectTemplateResource;
use Filament\Resources\Pages\ListRecords;
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
