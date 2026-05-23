<?php

namespace App\Filament\Hr\Resources\EmployeeResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class SkillsRelationManager extends RelationManager
{
    protected static string $relationship = 'skills';

    public static function getTitle(\Illuminate\Database\Eloquent\Model $ownerRecord, string $panelClass): string
    {
        return __('filament.relation.employee_skills');
    }

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')->label(__('filament.fields.skill'))->required(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name')->label(__('filament.columns.skill_name'))->badge(),
            ])
            ->headerActions([
                Tables\Actions\AttachAction::make()->label(__('filament.actions.add_skill_to_employee'))->preloadRecordSelect(),
                Tables\Actions\CreateAction::make()->label(__('filament.actions.create_new_skill')),
            ])
            ->actions([
                Tables\Actions\DetachAction::make()->label(__('filament.actions.remove')),
            ]);
    }
}