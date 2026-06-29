<?php

namespace App\Filament\Resources\JobApplicantResource\Pages;

use App\Filament\Resources\JobApplicantResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditJobApplicant extends EditRecord
{
    protected static string $resource = JobApplicantResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
