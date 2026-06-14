<?php

namespace App\Filament\Hr\Resources;

use App\Filament\Hr\Resources\TrainingResource\Pages;
use App\Filament\Hr\Resources\TrainingResource\RelationManagers\EmployeesRelationManager;
use App\Models\Training;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class TrainingResource extends Resource
{
    protected static ?string $model = Training::class;
    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';
    protected static ?string $navigationGroup = null;
    public static function getNavigationGroup(): ?string
    {
        return __('filament.group.training_development');
    }


    public static function getNavigationLabel(): string { return __('filament.nav.trainings'); }
    public static function getModelLabel(): string { return __('filament.model.training'); }
    public static function getPluralModelLabel(): string { return __('filament.model.trainings'); }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make(__('filament.sections.training_data'))
                ->schema([
                    Forms\Components\TextInput::make('title')
                        ->label(__('filament.fields.training_title'))
                        ->required()
                        ->maxLength(255),
                    Forms\Components\Textarea::make('description')
                        ->label(__('filament.fields.description'))
                        ->columnSpanFull(),
                    Forms\Components\TextInput::make('trainer')
                        ->label(__('filament.fields.trainer'))
                        ->maxLength(255),
                    Forms\Components\DatePicker::make('start_date')
                        ->label(__('filament.fields.start_date'))
                        ->required(),
                    Forms\Components\DatePicker::make('end_date')
                        ->label(__('filament.fields.end_date'))
                        ->required(),
                    Forms\Components\Select::make('status')
                        ->label(__('filament.fields.status'))
                        ->options([
                            'upcoming' => __('filament.training.upcoming'),
                            'ongoing' => __('filament.training.ongoing'),
                            'completed' => __('filament.training.completed'),
                        ])->default('upcoming'),
                    Forms\Components\TextInput::make('location')
                        ->label(__('filament.fields.location'))
                        ->maxLength(255),
                    Forms\Components\TextInput::make('max_participants')
                        ->label(__('filament.fields.max_participants'))
                        ->numeric()
                        ->minValue(1),
                ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label(__('filament.fields.training_title'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('trainer')
                    ->label(__('filament.fields.trainer'))
                    ->searchable(),
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
                        'upcoming' => 'info',
                        'ongoing' => 'warning',
                        'completed' => 'success',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'upcoming' => __('filament.training.upcoming'),
                        'ongoing' => __('filament.training.ongoing'),
                        'completed' => __('filament.training.completed'),
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('location')
                    ->label(__('filament.fields.location'))
                    ->searchable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label(__('filament.filters.training_status'))
                    ->options([
                        'upcoming' => __('filament.training.upcoming'),
                        'ongoing' => __('filament.training.ongoing'),
                        'completed' => __('filament.training.completed'),
                    ]),
            ])
            ->actions([Tables\Actions\EditAction::make()])
            ->bulkActions([Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()])]);
    }

    public static function getRelations(): array
    {
        return [EmployeesRelationManager::class];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTrainings::route('/'),
            'create' => Pages\CreateTraining::route('/create'),
            'edit' => Pages\EditTraining::route('/{record}/edit'),
        ];
    }
}
