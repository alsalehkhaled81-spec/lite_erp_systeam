<?php

namespace App\Filament\Hr\Resources;

use App\Filament\Hr\Resources\CareerPlanResource\Pages;
use App\Models\CareerPlan;
use App\Models\Employee;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CareerPlanResource extends Resource
{
    protected static ?string $model = CareerPlan::class;
    protected static ?string $navigationIcon = 'heroicon-o-rocket-launch';
    protected static ?string $navigationGroup = null;
    public static function getNavigationGroup(): ?string
    {
        return __('filament.group.training_development');
    }


    public static function getNavigationLabel(): string { return __('filament.nav.career_plans'); }
    public static function getModelLabel(): string { return __('filament.model.career_plan'); }
    public static function getPluralModelLabel(): string { return __('filament.model.career_plans'); }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make(__('filament.sections.career_plan_data'))
                ->schema([
                    Forms\Components\Select::make('employee_id')
                        ->label(__('filament.fields.employee'))
                        ->options(
                            Employee::with('user')
                                ->whereDoesntHave('user.role', fn ($query) => $query->where('name', 'super_admin'))
                                ->get()
                                ->pluck('user.name', 'id')
                        )
                        ->searchable()
                        ->required(),
                    Forms\Components\TextInput::make('current_role')
                        ->label(__('filament.fields.current_role'))
                        ->required(),
                    Forms\Components\TextInput::make('target_role')
                        ->label(__('filament.fields.target_role'))
                        ->required(),
                    Forms\Components\TextInput::make('timeline_months')
                        ->label(__('filament.fields.timeline_months'))
                        ->numeric()
                        ->minValue(0)
                        ->required()
                        ->suffix(__('filament.fields.months_unit')),
                    Forms\Components\Textarea::make('required_skills')
                        ->label(__('filament.fields.required_skills'))
                        ->columnSpanFull(),
                    Forms\Components\Textarea::make('notes')
                        ->label(__('filament.fields.notes'))
                        ->columnSpanFull(),
                    Forms\Components\Select::make('status')
                        ->label(__('filament.fields.status'))
                        ->options([
                            'draft' => __('filament.career.draft'),
                            'active' => __('filament.career.active'),
                            'completed' => __('filament.career.completed'),
                        ])->default('draft'),
                ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('employee.user.name')
                    ->label(__('filament.columns.employee_name'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('current_role')
                    ->label(__('filament.fields.current_role'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('target_role')
                    ->label(__('filament.fields.target_role'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('timeline_months')
                    ->label(__('filament.fields.timeline_months'))
                    ->suffix(' ' . __('filament.fields.months_unit')),
                Tables\Columns\TextColumn::make('status')
                    ->label(__('filament.columns.status'))
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'draft' => 'gray',
                        'active' => 'warning',
                        'completed' => 'success',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'draft' => __('filament.career.draft'),
                        'active' => __('filament.career.active'),
                        'completed' => __('filament.career.completed'),
                        default => $state,
                    }),
            ])
            ->filters([])
            ->actions([Tables\Actions\EditAction::make()])
            ->bulkActions([Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()])]);
    }

    public static function getRelations(): array { return []; }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCareerPlans::route('/'),
            'create' => Pages\CreateCareerPlan::route('/create'),
            'edit' => Pages\EditCareerPlan::route('/{record}/edit'),
        ];
    }
}
