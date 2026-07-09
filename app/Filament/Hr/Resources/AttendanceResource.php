<?php

namespace App\Filament\Hr\Resources;

use App\Filament\Hr\Resources\AttendanceResource\Pages;
use App\Models\Attendance;
use App\Models\Employee;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class AttendanceResource extends Resource
{
    protected static ?string $model = Attendance::class;
    protected static ?string $navigationIcon = 'heroicon-o-clock';
    protected static ?string $navigationGroup = null;
    public static function getNavigationGroup(): ?string
    {
        return __('filament.group.employee_management');
    }


    public static function getNavigationLabel(): string
    {
        return __('filament.nav.attendance');
    }

    public static function getModelLabel(): string
    {
        return __('filament.model.attendance');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament.model.attendances');
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make(__('filament.sections.attendance_data'))
                ->schema([
                    Forms\Components\Select::make('employee_id')
                        ->label(__('filament.fields.employee'))
                        ->options(Employee::with('user')->get()->pluck('user.name', 'id'))
                        ->searchable()
                        ->required()
                        ->live(),
                    Forms\Components\DatePicker::make('date')
                        ->label(__('filament.fields.date'))
                        ->required()
                        ->default(now())
                        ->live()
                        ->rule(function (Forms\Get $get, ?Attendance $record) {
                            return function (string $attribute, $value, \Closure $fail) use ($get, $record) {
                                $employeeId = $get('employee_id');
                                if ($employeeId && $value) {
                                    $exists = Attendance::where('employee_id', $employeeId)
                                        ->whereDate('date', $value)
                                        ->when($record, fn ($q) => $q->where('id', '!=', $record->id))
                                        ->exists();
                                    if ($exists) {
                                        $fail(__('filament.validation.duplicate_attendance'));
                                    }
                                }
                            };
                        }),
                    Forms\Components\TimePicker::make('check_in')
                        ->label(__('filament.fields.check_in_time'))
                        ->seconds(false)
                        ->live()
                        ->afterStateUpdated(function (Forms\Get $get, Forms\Set $set) {
                            $in = $get('check_in');
                            $out = $get('check_out');
                            if ($in && $out) {
                                $hours = Attendance::calculateHoursFromTimes($in, $out);
                                $set('hours_worked', $hours);
                                $set('overtime_hours', max(0, $hours - 8));
                                if ($hours < 4) {
                                    $set('status', 'absent');
                                } elseif ($hours < 7) {
                                    $set('status', 'half_day');
                                } else {
                                    $inTime = is_string($in) ? $in : $in->format('H:i:s');
                                    $outTime = is_string($out) ? $out : $out->format('H:i:s');
                                    if ($inTime < '09:00:00' || ($outTime >= '17:00:00' && $hours > 8)) {
                                        $set('status', 'over_time');
                                    } else {
                                        $set('status', $inTime > '09:15:00' ? 'late' : 'present');
                                    }
                                }
                            }
                        }),
                    Forms\Components\TimePicker::make('check_out')
                        ->label(__('filament.fields.check_out_time'))
                        ->seconds(false)
                        ->live()
                        ->rule(function (Forms\Get $get) {
                            return function (string $attribute, $value, \Closure $fail) use ($get) {
                                $checkIn = $get('check_in');
                                if ($checkIn && $value) {
                                    if (strtotime($value) < strtotime($checkIn)) {
                                        $fail(__('filament.validation.checkout_after_checkin'));
                                    }
                                }
                            };
                        })
                        ->afterStateUpdated(function (Forms\Get $get, Forms\Set $set) {
                            $in = $get('check_in');
                            $out = $get('check_out');
                            if ($in && $out) {
                                $hours = Attendance::calculateHoursFromTimes($in, $out);
                                $set('hours_worked', $hours);
                                $set('overtime_hours', max(0, $hours - 8));
                                if ($hours < 4) {
                                    $set('status', 'absent');
                                } elseif ($hours < 7) {
                                    $set('status', 'half_day');
                                } else {
                                    $inTime = is_string($in) ? $in : $in->format('H:i:s');
                                    $outTime = is_string($out) ? $out : $out->format('H:i:s');
                                    if ($inTime < '09:00:00' || ($outTime >= '17:00:00' && $hours > 8)) {
                                        $set('status', 'over_time');
                                    } else {
                                        $set('status', $inTime > '09:15:00' ? 'late' : 'present');
                                    }
                                }
                            }
                        }),
                    Forms\Components\TextInput::make('hours_worked')
                        ->label(__('filament.fields.hours_worked'))
                        ->numeric()
                        ->readOnly()
                        ->suffix(__('filament.fields.hours_unit'))
                        ->helperText(__('filament.fields.hours_worked_auto'))
                        ->default(0),
                    Forms\Components\TextInput::make('overtime_hours')
                        ->label(__('filament.fields.overtime_hours'))
                        ->numeric()
                        ->readOnly()
                        ->suffix(__('filament.fields.hours_unit'))
                        ->default(0),
                    Forms\Components\Select::make('status')
                        ->label(__('filament.fields.status'))
                        ->options([
                            'present' => __('filament.attendance.present'),
                            'late' => __('filament.attendance.late'),
                            'absent' => __('filament.attendance.absent'),
                            'half_day' => __('filament.attendance.half_day'),
                            'over_time' => __('filament.attendance.over_time'),
                        ])->default('present'),
                    Forms\Components\Textarea::make('notes')
                        ->label(__('filament.fields.notes'))
                        ->columnSpanFull(),
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
                Tables\Columns\TextColumn::make('date')
                    ->label(__('filament.fields.date'))
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('check_in')
                    ->label(__('filament.fields.check_in'))
                    ->dateTime('H:i'),
                Tables\Columns\TextColumn::make('check_out')
                    ->label(__('filament.fields.check_out'))
                    ->dateTime('H:i'),
                Tables\Columns\TextColumn::make('hours_worked')
                    ->label(__('filament.fields.hours_worked'))
                    ->suffix(' ' . __('filament.fields.hours_unit')),
                Tables\Columns\TextColumn::make('overtime_hours')
                    ->label(__('filament.fields.overtime_hours'))
                    ->suffix(' ' . __('filament.fields.hours_unit')),
                Tables\Columns\TextColumn::make('status')
                    ->label(__('filament.columns.status'))
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'present' => 'success',
                        'late' => 'warning',
                        'absent' => 'danger',
                        'half_day' => 'info',
                        'over_time' => 'primary',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'present' => __('filament.attendance.present'),
                        'late' => __('filament.attendance.late'),
                        'absent' => __('filament.attendance.absent'),
                        'half_day' => __('filament.attendance.half_day'),
                        'over_time' => __('filament.attendance.over_time'),
                        default => $state,
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('employee_id')
                    ->label(__('filament.fields.employee'))
                    ->options(Employee::with('user')->get()->pluck('user.name', 'id'))
                    ->searchable(),
                Tables\Filters\SelectFilter::make('status')
                    ->label(__('filament.filters.attendance_status'))
                    ->options([
                        'present' => __('filament.attendance.present'),
                        'late' => __('filament.attendance.late'),
                        'absent' => __('filament.attendance.absent'),
                        'half_day' => __('filament.attendance.half_day'),
                        'over_time' => __('filament.attendance.over_time'),
                    ]),
                Tables\Filters\Filter::make('date')
                    ->label(__('filament.fields.date'))
                    ->form([
                        Forms\Components\DatePicker::make('date_from')->label(__('filament.fields.date_from'))->live(),
                        Forms\Components\DatePicker::make('date_to')->label(__('filament.fields.date_to'))
                            ->minDate(fn (Forms\Get $get): ?string => $get('date_from') ?: null)
                            ->rule('after_or_equal:date_from')
                            ->validationMessages([
                                'after_or_equal' => __('filament.validation.end_date_after_start', ['attribute' => __('filament.fields.date_to')]),
                            ]),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['date_from'], fn ($q) => $q->where('date', '>=', $data['date_from']))
                            ->when($data['date_to'], fn ($q) => $q->where('date', '<=', $data['date_to']));
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('date', 'desc');
    }

    public static function getRelations(): array { return []; }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAttendances::route('/'),
            'create' => Pages\CreateAttendance::route('/create'),
            'edit' => Pages\EditAttendance::route('/{record}/edit'),
            'daily' => Pages\DailyAttendance::route('/daily'),
        ];
    }
}
