<?php

namespace App\Filament\Pm\Resources\TaskResource\Pages;

use App\Filament\Pm\Resources\TaskResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateTask extends CreateRecord
{
    protected static string $resource = TaskResource::class;
}
