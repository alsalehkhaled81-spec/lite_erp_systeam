<?php

namespace App\Filament\Resources\ProjectTemplateResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class TaskTemplatesRelationManager extends RelationManager
{
    protected static string $relationship = 'taskTemplates';

    public static function getTitle(\Illuminate\Database\Eloquent\Model $ownerRecord, string $panelClass): string
    {
        return __('filament.relation.task_templates');
    }

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('title')
                ->label(__('filament.fields.task_title'))
                ->required(),
            Forms\Components\Textarea::make('description')
                ->label(__('filament.fields.description'))
                ->rows(2)
                ->columnSpanFull(),
            Forms\Components\Select::make('priority')
                ->label(__('filament.fields.priority'))
                ->options([
                    'low' => __('filament.priority.low'),
                    'medium' => __('filament.priority.medium'),
                    'high' => __('filament.priority.high'),
                ])->default('medium'),
            Forms\Components\TextInput::make('estimated_hours')
                ->label(__('filament.fields.estimated_hours'))
                ->numeric()
                ->minValue(0)
                ->suffix(__('filament.fields.hours_unit')),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label(__('filament.fields.task_title'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('priority')
                    ->label(__('filament.fields.priority'))
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'low' => 'info',
                        'medium' => 'warning',
                        'high' => 'danger',
                    }),
                Tables\Columns\TextColumn::make('estimated_hours')
                    ->label(__('filament.fields.estimated_hours'))
                    ->suffix(' ' . __('filament.fields.hours_unit')),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label(__('filament.actions.new_task')),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->reorderable('sort_order');
    }
}
