<?php

namespace App\Filament\Resources\ResumeResource\Pages;

use App\Filament\Resources\ResumeResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListResumes extends ListRecords
{
    protected static string $resource = ResumeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
