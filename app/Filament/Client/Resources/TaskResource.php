<?php

namespace App\Filament\Client\Resources;

use App\Filament\Client\Resources\TaskResource\Pages;
use App\Models\Task;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class TaskResource extends Resource
{
    protected static ?string $model = Task::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    public static function getModelLabel(): string
    {
        return __('filament.model.task');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament.model.tasks');
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit($record): bool
    {
        return false;
    }

    public static function canDelete($record): bool
    {
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label(__('filament.fields.title'))
                    ->searchable()
                    ->sortable()
                    ->wrap(),
                Tables\Columns\TextColumn::make('project.name')
                    ->label(__('filament.fields.project'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label(__('filament.fields.status'))
                    ->formatStateUsing(fn (string $state): string => __("filament.status.{$state}"))
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'gray',
                        'in_progress' => 'warning',
                        'completed' => 'success',
                        'under_review' => 'info',
                        default => 'gray',
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('priority')
                    ->label(__('filament.fields.priority'))
                    ->formatStateUsing(fn (string $state): string => __("filament.priority.{$state}"))
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'low' => 'gray',
                        'medium' => 'warning',
                        'high' => 'danger',
                        'urgent' => 'danger',
                        default => 'gray',
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('start_date')
                    ->label(__('filament.fields.start_date'))
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('due_date')
                    ->label(__('filament.fields.due_date'))
                    ->date()
                    ->sortable(),
            ])
            ->filters([])
            ->actions([])
            ->bulkActions([]);
    }

    public static function getEloquentQuery(): Builder
    {
        $clientId = auth('client')->id();
        return parent::getEloquentQuery()
            ->whereHas('project', function (Builder $query) use ($clientId) {
                $query->where('client_id', $clientId);
            });
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTasks::route('/'),
        ];
    }
}
