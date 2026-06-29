<?php

namespace App\Filament\Client\Resources\ProjectResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class TasksRelationManager extends RelationManager
{
    protected static string $relationship = 'tasks';

    public static function getTitle(Model $ownerRecord, string $panelClass): string
    {
        return __('filament.relation.project_tasks');
    }

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('title')
                ->label(__('filament.fields.task_title')),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label(__('filament.columns.task')),
                Tables\Columns\TextColumn::make('employee.user.name')
                    ->label(__('filament.columns.responsible')),
                Tables\Columns\TextColumn::make('priority')
                    ->label(__('filament.fields.priority'))
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'low' => 'gray',
                        'medium' => 'info',
                        'high' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('due_date')
                    ->label(__('filament.columns.due_date'))
                    ->date(),
                Tables\Columns\TextColumn::make('status')
                    ->label(__('filament.columns.status'))
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'todo' => 'gray',
                        'in_progress' => 'warning',
                        'review' => 'info',
                        'done' => 'success',
                    }),
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
