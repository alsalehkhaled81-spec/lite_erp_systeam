<?php

namespace App\Filament\Pm\Resources\ProjectResource\RelationManagers;

use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class EmployeesRelationManager extends RelationManager
{
    protected static string $relationship = 'employees';

    public static function getTitle(\Illuminate\Database\Eloquent\Model $ownerRecord, string $panelClass): string
    {
        return __('filament.relation.project_team');
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitle(fn ($record) => $record->user->name ?? __('filament.fields.employee'))
            ->columns([
                Tables\Columns\TextColumn::make('user.name')->label(__('filament.columns.employee_name'))->searchable(),
                Tables\Columns\TextColumn::make('job_title')->label(__('filament.columns.job_title'))->badge(),
            ])
            ->headerActions([
                Tables\Actions\AttachAction::make()->label(__('filament.actions.add_employee_to_project'))->preloadRecordSelect(),
            ])
            ->actions([
                Tables\Actions\DetachAction::make()->label(__('filament.actions.remove_from_project')),
            ]);
    }
}