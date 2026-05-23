<?php

namespace App\Filament\Resources\ProjectResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class TasksRelationManager extends RelationManager
{
    protected static string $relationship = 'tasks';

    public static function getTitle(\Illuminate\Database\Eloquent\Model $ownerRecord, string $panelClass): string
    {
        return __('filament.relation.project_tasks');
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('employee_id')
                    ->label(__('filament.fields.responsible_employee'))
                    ->relationship('employee.user', 'name')
                    ->searchable()
                    ->required(),
                Forms\Components\TextInput::make('title')
                    ->label(__('filament.fields.task_title'))
                    ->required(),
                Forms\Components\DatePicker::make('due_date')
                    ->label(__('filament.fields.due_date')),
                Forms\Components\Select::make('status')
                    ->label(__('filament.fields.task_status'))
                    ->options([
                        'todo' => __('filament.status.todo'),
                        'in_progress' => __('filament.status.in_progress'),
                        'review' => __('filament.status.review'),
                        'done' => __('filament.status.done'),
                    ])->default('todo'),
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
                Tables\Columns\TextColumn::make('status')
                    ->label(__('filament.columns.status'))
                    ->badge(),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()->label(__('filament.actions.new_task')),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
