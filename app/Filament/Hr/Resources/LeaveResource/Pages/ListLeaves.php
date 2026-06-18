<?php

namespace App\Filament\Hr\Resources\LeaveResource\Pages;

use App\Filament\Hr\Resources\LeaveResource;
use Filament\Resources\Pages\ListRecords;

class ListLeaves extends ListRecords
{
    protected static string $resource = LeaveResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\CreateAction::make(),
        ];
    }
}
