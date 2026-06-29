<?php

namespace App\Filament\Client\Resources\ProjectResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class TeamRelationManager extends RelationManager
{
    protected static string $relationship = 'employees';

    public static function getTitle(Model $ownerRecord, string $panelClass): string
    {
        return __('filament.relation.project_team');
    }

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('id'),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label(__('filament.columns.employee_name')),
                Tables\Columns\TextColumn::make('job_title')
                    ->label(__('filament.fields.job_title')),
                Tables\Columns\TextColumn::make('department.name')
                    ->label(__('filament.columns.department')),
            ])
            ->headerActions([])
            ->actions([])
            ->bulkActions([]);
    }

    public function isReadOnly(): bool
    {
        return true;
    }
}
