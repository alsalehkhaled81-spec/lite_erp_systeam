<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Resources\TaskResource\Pages;
use App\Filament\Resources\TaskResource\RelationManagers;
use App\Models\Task;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TaskResource extends Resource
{
    protected static ?string $model = Task::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('الإسناد والتعيين')
                    ->schema([
                        Forms\Components\Select::make('project_id')
                            ->label('المشروع')
                            ->relationship('project', 'name')
                            ->required()
                            ->searchable(),
                        Forms\Components\Select::make('employee_id')
                            ->label('الموظف المسؤول')
                            ->relationship('employee.user', 'name')
                            ->required()
                            ->searchable(),
                    ])->columns(2),

                Forms\Components\Section::make('تفاصيل المهمة')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label('عنوان المهمة')
                            ->required(),
                        Forms\Components\Textarea::make('description')
                            ->label('الوصف')
                            ->columnSpanFull(),
                        Forms\Components\DatePicker::make('due_date')
                            ->label('تاريخ التسليم'),
                        Forms\Components\Select::make('status')
                            ->label('حالة المهمة')
                            ->options([
                                'todo' => 'مطلوبة',
                                'in_progress' => 'قيد التنفيذ',
                                'review' => 'للمراجعة',
                                'done' => 'منتهية',
                            ])->default('todo'),
                    ])->columns(2),
            ]);
    }


    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('المهمة')
                    ->searchable(),
                Tables\Columns\TextColumn::make('project.name')
                    ->label('المشروع')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('employee.user.name')
                    ->label('الموظف')
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('الحالة')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'todo' => 'gray',
                        'in_progress' => 'warning',
                        'review' => 'info',
                        'done' => 'success',
                    }),
                Tables\Columns\TextColumn::make('due_date')
                    ->label('التسليم')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('employee.id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('project_id')
                ->label('تصفية بالمشروع')
                ->relationship('project', 'name'),
            Tables\Filters\SelectFilter::make('status')
                ->label('حالة المهمة')
                ->options([
                            'todo' => 'مطلوبة',
                            'in_progress' => 'قيد التنفيذ',
                            'review' => 'للمراجعة',
                            'done' => 'منتهية',
                        ]),

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

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTasks::route('/'),
            'create' => Pages\CreateTask::route('/create'),
            'edit' => Pages\EditTask::route('/{record}/edit'),
        ];
    }
}
