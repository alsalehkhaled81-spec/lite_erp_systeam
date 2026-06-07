<?php

namespace App\Filament\Resources\ProjectTemplateResource\Pages;

use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\ProjectTemplateResource;
use Filament\Actions;

class EditProjectTemplate extends EditRecord
{
    protected static string $resource = ProjectTemplateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
