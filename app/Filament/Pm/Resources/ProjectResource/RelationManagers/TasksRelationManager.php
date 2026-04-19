<?php

namespace App\Filament\Pm\Resources\ProjectResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class TasksRelationManager extends RelationManager
{
    protected static string $relationship = 'tasks';
    protected static ?string $title = 'مهام المشروع';

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('employee_id')
                ->label('الموظف المسؤول')
                ->relationship('employee.user', 'name')
                ->required(),
            Forms\Components\TextInput::make('title')
                ->label('عنوان المهمة')->required(),
            Forms\Components\DatePicker::make('due_date')
                ->label('تاريخ التسليم'),
            Forms\Components\Select::make('status')
                ->label('الحالة')
                ->options([
                    'todo' => 'مطلوبة',
                    'in_progress' => 'قيد التنفيذ',
                    'review' => 'للمراجعة',
                    'done' => 'منتهية',
                ])->default('todo'),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')->label('المهمة'),
                Tables\Columns\TextColumn::make('employee.user.name')->label('المسؤول'),
                Tables\Columns\TextColumn::make('status')->label('الحالة')->badge()
                ->color(fn (string $state): string => match ($state) {
                    'todo' => 'gray', 'in_progress' => 'warning', 'review' => 'info', 'done' => 'success',
                }),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()->label('مهمة جديدة'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }
}
