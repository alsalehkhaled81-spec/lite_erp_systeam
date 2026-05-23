<?php

namespace App\Filament\Resources\ProjectResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Actions\AttachAction;
use Filament\Tables\Actions\DetachAction;

class EmployeesRelationManager extends RelationManager
{
    protected static string $relationship = 'employees';

    public static function getTitle(\Illuminate\Database\Eloquent\Model $ownerRecord, string $panelClass): string
    {
        return __('filament.relation.project_team');
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('job_title')
                    ->label(__('filament.fields.job_title'))
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label(__('filament.columns.employee_name')),
                Tables\Columns\TextColumn::make('job_title')
                    ->label(__('filament.columns.job_title')),
            ])
            ->headerActions([
                AttachAction::make()->label(__('filament.actions.add_employee_to_project')),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                DetachAction::make()->label(__('filament.actions.remove_from_project')),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DetachBulkAction::make(),
                ]),
            ]);
    }
}
