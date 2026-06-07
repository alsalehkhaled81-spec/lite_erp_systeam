<?php

namespace App\Filament\Pm\Resources;

use App\Filament\Pm\Resources\ProjectResource\Pages;
use App\Filament\Pm\Resources\ProjectResource\RelationManagers;
use App\Models\Project;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ProjectResource extends Resource
{
    protected static ?string $model = Project::class;
    protected static ?string $navigationIcon = 'heroicon-o-briefcase';
    protected static ?string $navigationGroup = 'المشاريع والمهام';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('filament.sections.project_details'))
                    ->schema([
                        Forms\Components\Select::make('client_id')
                            ->label(__('filament.fields.client'))
                            ->relationship('client', 'name')
                            ->searchable(),
                        Forms\Components\TextInput::make('name')
                            ->label(__('filament.fields.project_name'))
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Textarea::make('description')
                            ->label(__('filament.fields.project_description'))
                            ->columnSpanFull(),
                    ])->columns(2),
                Forms\Components\Section::make(__('filament.sections.financial_temporal'))
                    ->schema([
                        Forms\Components\TextInput::make('budget')
                            ->label(__('filament.fields.budget'))
                            ->numeric()
                            ->prefix('$'),
                        Forms\Components\Select::make('status')
                            ->label(__('filament.fields.project_status'))
                            ->options([
                                'pending' => __('filament.status.pending'),
                                'in_progress' => __('filament.status.in_progress'),
                                'completed' => __('filament.status.completed'),
                                'canceled' => __('filament.status.canceled'),
                            ])->default('pending'),
                        Forms\Components\DatePicker::make('start_date')
                            ->label(__('filament.fields.start_date')),
                        Forms\Components\DatePicker::make('end_date')
                            ->label(__('filament.fields.end_date')),
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
                Tables\Columns\TextColumn::make('client.name')
                    ->label(__('filament.columns.client'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('budget')
                    ->label(__('filament.columns.budget'))
                    ->money('usd')
                    ->sortable(),
                Tables\Columns\TextColumn::make('start_date')->date()->sortable(),
                Tables\Columns\TextColumn::make('end_date')->date()->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label(__('filament.columns.status'))
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'pending' => 'gray',
                        'in_progress' => 'warning',
                        'completed' => 'success',
                        'canceled' => 'danger',
                    }),
                Tables\Columns\TextColumn::make('completion_percentage')
                    ->label(__('filament.columns.completion_percentage'))
                    ->formatStateUsing(fn ($state) => $state . '%')
                    ->badge()
                    ->color(fn ($state) => $state >= 100 ? 'success' : ($state >= 50 ? 'warning' : 'danger')),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
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
            ->actions([Tables\Actions\EditAction::make()])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\EmployeesRelationManager::class,
            RelationManagers\TasksRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProjects::route('/'),
            'create' => Pages\CreateProject::route('/create'),
            'edit' => Pages\EditProject::route('/{record}/edit'),
        ];
    }

    public static function getModelLabel(): string
    {
        return __('filament.model.project');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament.model.projects');
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
