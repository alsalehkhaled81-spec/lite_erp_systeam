<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PayrollResource\Pages;
use App\Models\Payroll;
use App\Models\Employee;
use App\Models\Attendance;
use App\Services\PayslipPdfService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PayrollResource extends Resource
{
    protected static ?string $model = Payroll::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $navigationGroup = null;
    public static function getNavigationGroup(): ?string
    {
        return __('filament.group.finance');
    }


    public static function getNavigationLabel(): string
    {
        return __('filament.nav.payrolls');
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
                            ->searchable()
                            ->getOptionLabelUsing(fn ($value): ?string => Employee::find($value)?->user?->name . ' - ' . Employee::find($value)?->job_title)
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
                    ])->columns(2),

                Forms\Components\Section::make(__('filament.sections.salary_breakdown'))
                    ->schema([
                        Forms\Components\TextInput::make('basic_salary')
                            ->label(__('filament.fields.basic_salary'))
                            ->numeric()->prefix('$')->minValue(0)->required()
                            ->reactive()
                            ->afterStateUpdated(fn (callable $set, callable $get) => self::recalculate($set, $get)),
                        Forms\Components\TextInput::make('bonuses')
                            ->label(__('filament.fields.bonuses'))
                            ->numeric()->prefix('$')->minValue(0)->default(0)
                            ->reactive()
                            ->afterStateUpdated(fn (callable $set, callable $get) => self::recalculate($set, $get)),
                        Forms\Components\TextInput::make('deductions')
                            ->label(__('filament.fields.deductions'))
                            ->numeric()->prefix('$')->minValue(0)->default(0)
                            ->reactive()
                            ->afterStateUpdated(fn (callable $set, callable $get) => self::recalculate($set, $get)),
                    ])->columns(3),

                Forms\Components\Section::make(__('filament.sections.allowances'))
                    ->schema([
                        Forms\Components\TextInput::make('housing_allowance')
                            ->label(__('filament.fields.housing_allowance'))
                            ->numeric()->prefix('$')->minValue(0)->default(0)
                            ->reactive()
                            ->afterStateUpdated(fn (callable $set, callable $get) => self::recalculate($set, $get)),
                        Forms\Components\TextInput::make('transport_allowance')
                            ->label(__('filament.fields.transport_allowance'))
                            ->numeric()->prefix('$')->minValue(0)->default(0)
                            ->reactive()
                            ->afterStateUpdated(fn (callable $set, callable $get) => self::recalculate($set, $get)),
                        Forms\Components\TextInput::make('phone_allowance')
                            ->label(__('filament.fields.phone_allowance'))
                            ->numeric()->prefix('$')->minValue(0)->default(0)
                            ->reactive()
                            ->afterStateUpdated(fn (callable $set, callable $get) => self::recalculate($set, $get)),
                    ])->columns(3),

                Forms\Components\Section::make(__('filament.sections.insurance_and_absence'))
                    ->schema([
                        Forms\Components\TextInput::make('social_insurance_rate')
                            ->label(__('filament.fields.social_insurance_rate'))
                            ->numeric()->suffix('%')->minValue(0)->maxValue(100)->default(0)
                            ->reactive()
                            ->afterStateUpdated(fn (callable $set, callable $get) => self::recalculate($set, $get)),
                        Forms\Components\TextInput::make('social_insurance_amount')
                            ->label(__('filament.fields.social_insurance_amount'))
                            ->numeric()->prefix('$')->disabled()->dehydrated(),
                        Forms\Components\TextInput::make('absence_days')
                            ->label(__('filament.fields.absence_days'))
                            ->numeric()->minValue(0)->default(0)
                            ->reactive()
                            ->afterStateUpdated(fn (callable $set, callable $get) => self::recalculate($set, $get)),
                        Forms\Components\TextInput::make('absence_deduction')
                            ->label(__('filament.fields.absence_deduction'))
                            ->numeric()->prefix('$')->disabled()->dehydrated(),
                        Forms\Components\Actions::make([
                            Forms\Components\Actions\Action::make('calculate_from_attendance')
                                ->label(__('filament.actions.calculate_from_attendance'))
                                ->icon('heroicon-o-calculator')
                                ->action(function (callable $set, callable $get) {
                                    $employeeId = $get('employee_id');
                                    $monthYear = $get('month_year');
                                    if (!$employeeId || !$monthYear) return;

                                    $date = \Carbon\Carbon::createFromFormat('Y-m', $monthYear);
                                    $absenceDays = Attendance::where('employee_id', $employeeId)
                                        ->where('status', 'absent')
                                        ->whereMonth('date', $date->month)
                                        ->whereYear('date', $date->year)
                                        ->count();

                                    $set('absence_days', $absenceDays);
                                    self::recalculate($set, $get);
                                }),
                        ]),
                    ])->columns(3),

                Forms\Components\Section::make(__('filament.sections.net_salary'))
                    ->schema([
                        Forms\Components\TextInput::make('net_salary')
                            ->label(__('filament.fields.net_salary'))
                            ->numeric()->prefix('$')->minValue(0)->required()->dehydrated(),
                        Forms\Components\Select::make('status')
                            ->label(__('filament.fields.status'))
                            ->options([
                                'paid' => __('filament.status.paid'),
                                'unpaid' => __('filament.status.unpaid'),
                            ])->default('unpaid')->required(),
                    ])->columns(2),
            ]);
    }

    private static function recalculate(callable $set, callable $get): void
    {
        $basic = (float)($get('basic_salary') ?? 0);
        $bonuses = (float)($get('bonuses') ?? 0);
        $deductions = (float)($get('deductions') ?? 0);
        $housing = (float)($get('housing_allowance') ?? 0);
        $transport = (float)($get('transport_allowance') ?? 0);
        $phone = (float)($get('phone_allowance') ?? 0);
        $insuranceRate = (float)($get('social_insurance_rate') ?? 0);
        $absenceDays = (int)($get('absence_days') ?? 0);

        $insuranceAmount = round($basic * ($insuranceRate / 100), 2);
        $absenceDeduction = round(($basic / 30) * $absenceDays, 2);

        $set('social_insurance_amount', $insuranceAmount);
        $set('absence_deduction', $absenceDeduction);
        $set('net_salary', max(0, round($basic + $bonuses + $housing + $transport + $phone - $deductions - $insuranceAmount - $absenceDeduction, 2)));
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('employee.user.name')
                    ->label(__('filament.columns.employee_name'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('month_year')
                    ->label(__('filament.columns.month'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('basic_salary')
                    ->label(__('filament.columns.basic_salary'))
                    ->money('usd')
                    ->sortable(),
                Tables\Columns\TextColumn::make('housing_allowance')
                    ->label(__('filament.columns.housing_allowance'))
                    ->money('usd')
                    ->toggleable(),
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
                Tables\Filters\SelectFilter::make('employee_id')
                    ->label(__('filament.fields.employee'))
                    ->relationship('employee', 'job_title'),
            ])
            ->actions([
                Tables\Actions\Action::make('download_payslip')
                    ->label(__('filament.actions.download_payslip'))
                    ->icon('heroicon-o-document-arrow-down')
                    ->action(fn (Payroll $record) => app(PayslipPdfService::class)->generate($record)),
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
        return [];
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
