<?php

namespace App\Filament\Hr\Resources\CareerPlanResource\Pages;

use App\Filament\Hr\Resources\CareerPlanResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCareerPlans extends ListRecords
{
    protected static string $resource = CareerPlanResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\CreateAction::make()];
    }
}
