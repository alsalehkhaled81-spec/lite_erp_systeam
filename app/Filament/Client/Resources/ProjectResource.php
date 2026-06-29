<?php

namespace App\Filament\Client\Resources;

use App\Filament\Client\Resources\ProjectResource\Pages;
use App\Filament\Client\Resources\ProjectResource\RelationManagers;
use App\Models\Project;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class ProjectResource extends Resource
{
    protected static ?string $model = Project::class;

    protected static ?string $navigationIcon = 'heroicon-o-briefcase';

    protected static ?string $navigationGroup = null;

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make(__('filament.sections.project_details'))
                    ->schema([
                        Infolists\Components\TextEntry::make('name')
                            ->label(__('filament.fields.project_name')),
                        Infolists\Components\TextEntry::make('description')
                            ->label(__('filament.fields.project_description'))
                            ->columnSpanFull(),
                        Infolists\Components\TextEntry::make('status')
                            ->label(__('filament.fields.project_status'))
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'pending' => 'gray',
                                'in_progress' => 'warning',
                                'completed' => 'success',
                                'canceled' => 'danger',
                            }),
                        Infolists\Components\TextEntry::make('completion_percentage')
                            ->label(__('filament.columns.completion_percentage'))
                            ->formatStateUsing(fn ($state) => $state.'%')
                            ->color(fn ($state) => $state >= 100 ? 'success' : ($state >= 50 ? 'warning' : 'danger')),
                    ])->columns(2),
                Infolists\Components\Section::make(__('filament.sections.financial_temporal'))
                    ->schema([
                        Infolists\Components\TextEntry::make('budget')
                            ->label(__('filament.fields.budget'))
                            ->money('usd'),
                        Infolists\Components\TextEntry::make('start_date')
                            ->label(__('filament.fields.start_date'))
                            ->date(),
                        Infolists\Components\TextEntry::make('end_date')
                            ->label(__('filament.fields.end_date'))
                            ->date(),
                        Infolists\Components\TextEntry::make('total_tracked_hours')
                            ->label(__('filament.client_portal.tracked_hours'))
                            ->formatStateUsing(fn ($state) => $state.' '.__('filament.fields.hours_unit')),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(__('filament.fields.project_name'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('budget')
                    ->label(__('filament.columns.budget'))
                    ->money('usd')
                    ->sortable(),
                Tables\Columns\TextColumn::make('start_date')
                    ->label(__('filament.columns.start_date'))
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('end_date')
                    ->label(__('filament.columns.end_date'))
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label(__('filament.columns.status'))
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'gray',
                        'in_progress' => 'warning',
                        'completed' => 'success',
                        'canceled' => 'danger',
                    }),
                Tables\Columns\TextColumn::make('completion_percentage')
                    ->label(__('filament.columns.completion_percentage'))
                    ->formatStateUsing(fn ($state) => $state.'%')
                    ->badge()
                    ->color(fn ($state) => $state >= 100 ? 'success' : ($state >= 50 ? 'warning' : 'danger')),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label(__('filament.filters.project_status'))
                    ->options([
                        'pending' => __('filament.status.pending'),
                        'in_progress' => __('filament.status.in_progress'),
                        'completed' => __('filament.status.completed'),
                        'canceled' => __('filament.status.canceled'),
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\TasksRelationManager::class,
            RelationManagers\InvoicesRelationManager::class,
            RelationManagers\TeamRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProjects::route('/'),
            'view' => Pages\ViewProject::route('/{record}'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('client_id', auth('client')->id());
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit(Model $record): bool
    {
        return false;
    }

    public static function canDelete(Model $record): bool
    {
        return false;
    }

    public static function getModelLabel(): string
    {
        return __('filament.model.project');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament.model.projects');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('filament.client_portal.my_projects');
    }

    public static function getNavigationLabel(): string
    {
        return __('filament.nav.projects');
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name'];
    }
}
