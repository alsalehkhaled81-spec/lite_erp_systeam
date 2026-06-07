<?php

namespace App\Filament\Pm\Resources\ProjectTemplateResource\Pages;

use App\Filament\Pm\Resources\ProjectTemplateResource;
use Filament\Resources\Pages\EditRecord;
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
