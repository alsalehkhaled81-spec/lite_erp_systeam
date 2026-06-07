<?php

namespace App\Filament\Pm\Resources\TaskResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class TimeEntriesRelationManager extends RelationManager
{
    protected static string $relationship = 'timeEntries';

    public static function getTitle(\Illuminate\Database\Eloquent\Model $ownerRecord, string $panelClass): string
    {
        return __('filament.relation.time_entries');
    }

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('employee_id')
                ->label(__('filament.fields.employee'))
                ->relationship('employee.user', 'name')
                ->required()
                ->searchable(),
            Forms\Components\DatePicker::make('date')
                ->label(__('filament.fields.date'))
                ->required()
                ->default(now()),
            Forms\Components\TextInput::make('hours')
                ->label(__('filament.fields.hours'))
                ->numeric()
                ->required()
                ->minValue(0.25)
                ->maxValue(24)
                ->step(0.25),
            Forms\Components\Textarea::make('description')
                ->label(__('filament.fields.description'))
                ->rows(2)
                ->columnSpanFull(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('employee.user.name')
                    ->label(__('filament.columns.employee_name'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('date')
                    ->label(__('filament.fields.date'))
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('hours')
                    ->label(__('filament.fields.hours'))
                    ->numeric()
                    ->suffix(' ' . __('filament.fields.hours_unit'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('description')
                    ->label(__('filament.fields.description'))
                    ->limit(50)
                    ->wrap(),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label(__('filament.actions.add_time_entry')),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->defaultSort('date', 'desc');
    }
}
