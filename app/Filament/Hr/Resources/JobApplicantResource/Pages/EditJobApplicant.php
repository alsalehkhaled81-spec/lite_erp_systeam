<?php

namespace App\Filament\Hr\Resources\JobApplicantResource\Pages;

use App\Filament\Hr\Resources\JobApplicantResource;
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
