<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VacancyResource\Pages;
use App\Filament\Resources\VacancyResource\RelationManagers;
use App\Models\Vacancy;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class VacancyResource extends Resource
{
    protected static ?string $model = Vacancy::class;

    protected static ?string $navigationIcon = 'heroicon-o-briefcase';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('filament.sections.vacancy_data'))
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label(__('filament.fields.vacancy_title'))
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),
                        Forms\Components\Select::make('department_id')
                            ->label(__('filament.fields.department'))
                            ->relationship('department', 'name')
                            ->searchable()
                            ->preload(),
                        Forms\Components\Select::make('employment_type')
                            ->label(__('filament.fields.employment_type'))
                            ->options([
                                'full_time' => __('filament.fields.employment_full_time'),
                                'part_time' => __('filament.fields.employment_part_time'),
                                'contract' => __('filament.fields.employment_contract'),
                                'internship' => __('filament.fields.employment_internship'),
                            ])
                            ->default('full_time')
                            ->required(),
                        Forms\Components\TextInput::make('location')
                            ->label(__('filament.fields.location'))
                            ->maxLength(255),
                        Forms\Components\TextInput::make('positions_count')
                            ->label(__('filament.fields.positions_count'))
                            ->numeric()
                            ->default(1)
                            ->minValue(1)
                            ->required(),
                        Forms\Components\TextInput::make('salary_min')
                            ->label(__('filament.fields.salary_min'))
                            ->numeric()
                            ->minValue(0),
                        Forms\Components\TextInput::make('salary_max')
                            ->label(__('filament.fields.salary_max'))
                            ->numeric()
                            ->minValue(0),
                        Forms\Components\Select::make('status')
                            ->label(__('filament.fields.status'))
                            ->options([
                                'open' => __('filament.status.vacancy_open'),
                                'closed' => __('filament.status.vacancy_closed'),
                            ])
                            ->default('open')
                            ->required(),
                    ])->columns(2),

                Forms\Components\Section::make(__('filament.sections.vacancy_details'))
                    ->schema([
                        Forms\Components\Textarea::make('description')
                            ->label(__('filament.fields.vacancy_description'))
                            ->rows(4)
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('requirements')
                            ->label(__('filament.fields.vacancy_requirements'))
                            ->hint(__('filament.fields.vacancy_requirements_hint'))
                            ->placeholder(__('filament.fields.vacancy_requirements_placeholder'))
                            ->rows(5)
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label(__('filament.fields.vacancy_title'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('department.name')
                    ->label(__('filament.fields.department'))
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('employment_type')
                    ->label(__('filament.fields.employment_type'))
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => __('filament.fields.employment_' . $state))
                    ->sortable(),
                Tables\Columns\TextColumn::make('applicants_count')
                    ->label(__('filament.columns.applicants_count'))
                    ->counts('applicants')
                    ->badge()
                    ->color('success')
                    ->sortable(),
                Tables\Columns\TextColumn::make('positions_count')
                    ->label(__('filament.fields.positions_count'))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('status')
                    ->label(__('filament.fields.status'))
                    ->badge()
                    ->color(fn (string $state): string => $state === 'open' ? 'success' : 'gray')
                    ->formatStateUsing(fn (string $state): string => __('filament.status.vacancy_' . $state))
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('filament.columns.created_at'))
                    ->date()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'open' => __('filament.status.vacancy_open'),
                        'closed' => __('filament.status.vacancy_closed'),
                    ]),
                Tables\Filters\SelectFilter::make('department')
                    ->relationship('department', 'name'),
                Tables\Filters\SelectFilter::make('employment_type')
                    ->label(__('filament.fields.employment_type'))
                    ->options([
                        'full_time' => __('filament.fields.employment_full_time'),
                        'part_time' => __('filament.fields.employment_part_time'),
                        'contract' => __('filament.fields.employment_contract'),
                        'internship' => __('filament.fields.employment_internship'),
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\ApplicantsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListVacancies::route('/'),
            'create' => Pages\CreateVacancy::route('/create'),
            'edit' => Pages\EditVacancy::route('/{record}/edit'),
        ];
    }

    public static function getModelLabel(): string
    {
        return __('filament.model.vacancy');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament.model.vacancies');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('filament.group.employee_management');
    }

    public static function getNavigationLabel(): string
    {
        return __('filament.nav.vacancies');
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', 'open')->count();
    }
}
