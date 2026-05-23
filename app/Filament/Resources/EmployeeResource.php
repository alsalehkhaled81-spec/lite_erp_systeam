<?php

namespace App\Filament\Resources;

use App\Filament\Hr\Resources\EmployeeResource\RelationManagers\SkillsRelationManager;
use App\Filament\Resources\EmployeeResource\Pages;
use App\Filament\Resources\EmployeeResource\RelationManagers;
use App\Models\Employee;
use App\Services\AiEvaluationService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class EmployeeResource extends Resource
{
    protected static ?string $model = Employee::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('filament.sections.employee_data'))
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->label(__('filament.fields.user_account'))
                            ->relationship('user', 'name')
                            ->required(),
                        Forms\Components\Select::make('department_id')
                            ->label(__('filament.fields.department'))
                            ->relationship('department', 'name')
                            ->searchable()
                            ->nullable(),
                        Forms\Components\TextInput::make('job_title')
                            ->label(__('filament.fields.job_title'))
                            ->maxLength(255),
                        Forms\Components\TextInput::make('salary')
                            ->label(__('filament.fields.salary'))
                            ->numeric()
                            ->minValue(0)
                            ->prefix('$'),
                        Forms\Components\Select::make('status')
                            ->label(__('filament.fields.employee_status'))
                            ->options([
                                'active' => __('filament.status.active'),
                                'on_leave' => __('filament.status.on_leave'),
                                'terminated' => __('filament.status.terminated'),
                            ])
                            ->default('active')
                            ->required(),
                        Forms\Components\DatePicker::make('hire_date')
                            ->label(__('filament.fields.hire_date')),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label(__('filament.columns.employee_name'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('department.name')
                    ->label(__('filament.fields.department'))
                    ->searchable()
                    ->default('—'),
                Tables\Columns\TextColumn::make('job_title')
                    ->label(__('filament.fields.job_title'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('salary')
                    ->label(__('filament.fields.salary'))
                    ->money('usd')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label(__('filament.columns.status'))
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'on_leave' => 'warning',
                        'terminated' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('hire_date')
                    ->label(__('filament.fields.hire_date'))
                    ->date()
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
                Tables\Filters\SelectFilter::make('status')
                    ->label(__('filament.filters.employee_status'))
                    ->options([
                        'active' => __('filament.status.active'),
                        'on_leave' => __('filament.status.on_leave'),
                        'terminated' => __('filament.status.terminated'),
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('ai_evaluate')
                    ->label(__('filament.actions.ai_evaluate'))
                    ->icon('heroicon-o-cpu-chip')
                    ->color('info')
                    ->modalHeading(__('filament.actions.ai_evaluate_heading'))
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel(__('filament.actions.close'))
                    ->modalContent(fn (Employee $record) => view('filament.pages.ai-evaluation', ['evaluation' => app(AiEvaluationService::class)->evaluate($record)]))
                    ->visible(fn (Employee $record) => $record->status === 'active'),
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
            SkillsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEmployees::route('/'),
            'create' => Pages\CreateEmployee::route('/create'),
            'edit' => Pages\EditEmployee::route('/{record}/edit'),
        ];
    }

    public static function getModelLabel(): string
    {
        return __('filament.model.employee');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament.model.employees');
    }

    public static function getNavigationLabel(): string
    {
        return __('filament.nav.employees');
    }
}
