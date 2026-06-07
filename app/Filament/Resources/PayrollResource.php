<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PayrollResource\Pages;
use App\Models\Payroll;
use App\Models\Employee;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PayrollResource extends Resource
{
    protected static ?string $model = Payroll::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    protected static ?string $navigationGroup = 'المالية';

    public static function getNavigationLabel(): string
    {
        return __('filament.model.payrolls');
    }

    public static function getModelLabel(): string
    {
        return __('filament.model.payroll');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament.model.payrolls');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('filament.sections.payroll_data'))
                    ->schema([
                        Forms\Components\Select::make('employee_id')
                            ->label(__('filament.fields.employee'))
                            ->relationship('employee', 'job_title')
                            ->searchable()
                            ->getSearchResultsUsing(fn (string $search) => Employee::where('job_title', 'like', "%{$search}%")->orWhereHas('user', fn ($q) => $q->where('name', 'like', "%{$search}%"))->limit(20)->get()->mapWithKeys(fn ($e) => [$e->id => $e->user?->name . ' - ' . $e->job_title]))
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(function (callable $set, $state) {
                                $employee = Employee::find($state);
                                if ($employee) {
                                    $set('basic_salary', $employee->salary);
                                }
                            }),
                        Forms\Components\TextInput::make('month_year')
                            ->label(__('filament.fields.month'))
                            ->type('month')
                            ->required(),
                        Forms\Components\TextInput::make('basic_salary')
                            ->label(__('filament.fields.basic_salary'))
                            ->numeric()
                            ->prefix('$')
                            ->minValue(0)
                            ->required(),
                        Forms\Components\TextInput::make('bonuses')
                            ->label(__('filament.fields.bonuses'))
                            ->numeric()
                            ->prefix('$')
                            ->minValue(0)
                            ->default(0)
                            ->reactive()
                            ->afterStateUpdated(fn (callable $set, callable $get) => $set('net_salary', Payroll::calculateNetSalary((float) ($get('basic_salary') ?? 0), (float) ($get('bonuses') ?? 0), (float) ($get('deductions') ?? 0)))),
                        Forms\Components\TextInput::make('deductions')
                            ->label(__('filament.fields.deductions'))
                            ->numeric()
                            ->prefix('$')
                            ->minValue(0)
                            ->default(0)
                            ->reactive()
                            ->afterStateUpdated(fn (callable $set, callable $get) => $set('net_salary', Payroll::calculateNetSalary((float) ($get('basic_salary') ?? 0), (float) ($get('bonuses') ?? 0), (float) ($get('deductions') ?? 0)))),
                        Forms\Components\TextInput::make('net_salary')
                            ->label(__('filament.fields.net_salary'))
                            ->numeric()
                            ->prefix('$')
                            ->minValue(0)
                            ->required(),
                        Forms\Components\Select::make('status')
                            ->label(__('filament.fields.status'))
                            ->options([
                                'paid' => __('filament.status.paid'),
                                'unpaid' => __('filament.status.unpaid'),
                            ])
                            ->default('unpaid')
                            ->required(),
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
                Tables\Columns\TextColumn::make('month_year')
                    ->label(__('filament.fields.month'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('basic_salary')
                    ->label(__('filament.columns.basic_salary'))
                    ->money('usd')
                    ->sortable(),
                Tables\Columns\TextColumn::make('bonuses')
                    ->label(__('filament.columns.bonuses'))
                    ->money('usd')
                    ->sortable(),
                Tables\Columns\TextColumn::make('deductions')
                    ->label(__('filament.columns.deductions'))
                    ->money('usd')
                    ->sortable(),
                Tables\Columns\TextColumn::make('net_salary')
                    ->label(__('filament.columns.net_salary'))
                    ->money('usd')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label(__('filament.columns.status'))
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'paid' => 'success',
                        'unpaid' => 'warning',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'paid' => __('filament.status.paid'),
                        'unpaid' => __('filament.status.unpaid'),
                        default => $state,
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label(__('filament.filters.payroll_status'))
                    ->options([
                        'paid' => __('filament.status.paid'),
                        'unpaid' => __('filament.status.unpaid'),
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
            'index' => Pages\ListPayrolls::route('/'),
            'create' => Pages\CreatePayroll::route('/create'),
            'edit' => Pages\EditPayroll::route('/{record}/edit'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['month_year'];
    }
}
