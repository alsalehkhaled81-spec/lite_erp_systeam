<?php

namespace App\Filament\Hr\Resources\CareerPlanResource\Pages;

use App\Filament\Hr\Resources\CareerPlanResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCareerPlan extends EditRecord
{
    protected static string $resource = CareerPlanResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()];
    }
}
