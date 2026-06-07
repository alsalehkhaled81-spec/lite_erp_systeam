<?php

namespace App\Filament\Employee\Resources;

use App\Filament\Employee\Resources\TaskResource\Pages;
use App\Models\Task;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class TaskResource extends Resource
{
    protected static ?string $model = Task::class;
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';
    protected static ?string $navigationGroup = 'المهام';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('filament.sections.update_task_status'))
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label(__('filament.fields.task_title'))
                            ->disabled(),
                        Forms\Components\Select::make('project_id')
                            ->label(__('filament.fields.project'))
                            ->relationship('project', 'name')
                            ->disabled(),
                        Forms\Components\Select::make('status')
                            ->label(__('filament.fields.change_status'))
                            ->options([
                                'todo' => __('filament.status.todo'),
                                'in_progress' => __('filament.status.in_progress'),
                                'review' => __('filament.status.review'),
                                'done' => __('filament.status.done'),
                            ])
                            ->required(),
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
                    ->label(__('filament.columns.project')),
                Tables\Columns\TextColumn::make('status')
                    ->label(__('filament.columns.status'))
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'todo' => 'gray',
                        'in_progress' => 'warning',
                        'review' => 'info',
                        'done' => 'success',
                    }),
                Tables\Columns\TextColumn::make('due_date')
                    ->label(__('filament.columns.due_date'))
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label(__('filament.filters.task_status'))
                    ->options([
                        'todo' => __('filament.status.todo'),
                        'in_progress' => __('filament.status.in_progress'),
                        'review' => __('filament.status.review'),
                        'done' => __('filament.status.done'),
                    ]),
            ])
            ->actions([Tables\Actions\EditAction::make()])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()]),
            ]);
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()->whereHas('employee', function ($query) {
            $query->where('user_id', auth()->id());
        });
    }

    public static function getRelations(): array { return []; }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTasks::route('/'),
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

    public static function getNavigationLabel(): string
    {
        return __('filament.nav.tasks');
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['title'];
    }
}
