<?php

namespace App\Filament\Client\Resources\TaskResource\Pages;

use App\Filament\Client\Resources\TaskResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTasks extends ListRecords
{
    protected static string $resource = TaskResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
