<?php

namespace App\Filament\Hr\Resources\EmployeeResource\Pages;

use App\Filament\Hr\Resources\EmployeeResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateEmployee extends CreateRecord
{
    protected static string $resource = EmployeeResource::class;
}
