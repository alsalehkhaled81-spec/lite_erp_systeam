<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TaskResource\Pages;
use App\Filament\Resources\TaskResource\RelationManagers\CommentsRelationManager;
use App\Filament\Resources\TaskResource\RelationManagers\AttachmentsRelationManager;
use App\Filament\Resources\TaskResource\RelationManagers\TimeEntriesRelationManager;
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

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';

    protected static ?string $navigationGroup = null;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('filament.sections.task_assignment'))
                    ->schema([
                        Forms\Components\Select::make('project_id')
                            ->label(__('filament.fields.project'))
                            ->relationship(
                                name: 'project',
                                titleAttribute: 'name',
                                modifyQueryUsing: function (Builder $query, Forms\Get $get) {
                                    if ($employeeId = $get('employee_id')) {
                                        $query->whereHas('employees', fn ($q) => $q->where('employees.id', $employeeId));
                                    }
                                }
                            )
                            ->required()
                            ->searchable()
                            ->preload()
                            ->live(),
                        Forms\Components\Select::make('employee_id')
                            ->label(__('filament.fields.responsible_employee'))
                            ->relationship(
                                name: 'employee.user',
                                titleAttribute: 'name',
                                modifyQueryUsing: function (Builder $query, Forms\Get $get) {
                                    if ($projectId = $get('project_id')) {
                                        $query->whereHas('employee.projects', fn ($q) => $q->where('projects.id', $projectId));
                                    }
                                }
                            )
                            ->required()
                            ->searchable()
                            ->preload()
                            ->live(),
                    ])->columns(2),

                Forms\Components\Section::make(__('filament.sections.task_details'))
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label(__('filament.fields.task_title'))
                            ->required(),
                        Forms\Components\Textarea::make('description')
                            ->label(__('filament.fields.description'))
                            ->columnSpanFull(),
                        Forms\Components\DatePicker::make('start_date')
                            ->label(__('filament.fields.start_date'))
                            ->live(),
                        Forms\Components\DatePicker::make('due_date')
                            ->label(__('filament.fields.due_date'))
                            ->minDate(fn (Forms\Get $get): ?string => $get('start_date') ?: null)
                            ->rule('after_or_equal:start_date')
                            ->validationMessages([
                                'after_or_equal' => __('filament.validation.due_date_after_start', ['attribute' => __('filament.fields.due_date')]),
                            ]),
                        Forms\Components\Select::make('status')
                            ->label(__('filament.fields.task_status'))
                            ->options([
                                'todo' => __('filament.status.todo'),
                                'in_progress' => __('filament.status.in_progress'),
                                'review' => __('filament.status.review'),
                                'done' => __('filament.status.done'),
                            ])->default('todo'),
                        Forms\Components\Select::make('priority')
                            ->label(__('filament.fields.priority'))
                            ->options([
                                'low' => __('filament.priority.low'),
                                'medium' => __('filament.priority.medium'),
                                'high' => __('filament.priority.high'),
                            ])->default('medium'),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label(__('filament.columns.task'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('project.name')
                    ->label(__('filament.columns.project'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('employee.user.name')
                    ->label(__('filament.fields.employee'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->label(__('filament.columns.status'))
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'todo' => 'gray',
                        'in_progress' => 'warning',
                        'review' => 'info',
                        'done' => 'success',
                    }),
                Tables\Columns\TextColumn::make('priority')
                    ->label(__('filament.fields.priority'))
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'low' => 'info',
                        'medium' => 'warning',
                        'high' => 'danger',
                    }),
                Tables\Columns\TextColumn::make('due_date')
                    ->label(__('filament.columns.due_date'))
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
                    ->label(__('filament.filters.filter_by_project'))
                    ->relationship('project', 'name'),
                Tables\Filters\SelectFilter::make('status')
                    ->label(__('filament.filters.task_status'))
                    ->options([
                        'todo' => __('filament.status.todo'),
                        'in_progress' => __('filament.status.in_progress'),
                        'review' => __('filament.status.review'),
                        'done' => __('filament.status.done'),
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
            CommentsRelationManager::class,
            AttachmentsRelationManager::class,
            TimeEntriesRelationManager::class,
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

    public static function getModelLabel(): string
    {
        return __('filament.model.task');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament.model.tasks');
    }
    public static function getNavigationGroup(): ?string
    {
        return __('filament.group.projects_tasks');
    }


    public static function getNavigationLabel(): string
    {
        return __('filament.nav.tasks');
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['title'];
    }
}
