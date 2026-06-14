<?php

namespace App\Filament\Hr\Resources;

use App\Filament\Hr\Resources\LeaveResource\Pages;
use App\Models\Leave;
use App\Notifications\LeaveStatusNotification;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class LeaveResource extends Resource
{
    protected static ?string $model = Leave::class;
    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?string $navigationGroup = null;
    public static function getNavigationGroup(): ?string
    {
        return __('filament.group.leaves');
    }


    public static function getNavigationLabel(): string
    {
        return __('filament.nav.leave_requests');
    }

    public static function getModelLabel(): string
    {
        return __('filament.model.leave_request');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament.model.leave_requests');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('filament.sections.leave_data'))
                    ->schema([
                        Forms\Components\TextInput::make('type')
                            ->label(__('filament.fields.leave_type'))
                            ->disabled(),
                        Forms\Components\DatePicker::make('start_date')
                            ->label(__('filament.fields.start_date'))
                            ->disabled(),
                        Forms\Components\DatePicker::make('end_date')
                            ->label(__('filament.fields.end_date'))
                            ->disabled(),
                        Forms\Components\Textarea::make('reason')
                            ->label(__('filament.fields.reason'))
                            ->disabled()
                            ->columnSpanFull(),
                        Forms\Components\Select::make('status')
                            ->label(__('filament.fields.status'))
                            ->options([
                                'pending' => __('filament.status.pending'),
                                'approved_by_head' => __('filament.status.approved_by_head'),
                                'approved_by_hr' => __('filament.status.approved_by_hr'),
                                'rejected' => __('filament.status.rejected'),
                            ])
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
                Tables\Columns\TextColumn::make('employee.remaining_leave_balance')
                    ->label(__('filament.columns.remaining_balance'))
                    ->badge()
                    ->color(fn ($state) => $state <= 3 ? 'danger' : ($state <= 10 ? 'warning' : 'success')),
                Tables\Columns\TextColumn::make('type')
                    ->label(__('filament.columns.type'))
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'Sick' => __('filament.leave_type.Sick'),
                        'Annual' => __('filament.leave_type.Annual'),
                        'Emergency' => __('filament.leave_type.Emergency'),
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('start_date')
                    ->label(__('filament.columns.start_date'))
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('end_date')
                    ->label(__('filament.columns.end_date'))
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('duration_in_days')
                    ->label(__('filament.fields.duration_days')),
                Tables\Columns\TextColumn::make('status')
                    ->label(__('filament.columns.status'))
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'approved_by_head' => 'info',
                        'approved_by_hr' => 'success',
                        'rejected' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => __('filament.status.pending'),
                        'approved_by_head' => __('filament.status.approved_by_head'),
                        'approved_by_hr' => __('filament.status.approved_by_hr'),
                        'rejected' => __('filament.status.rejected'),
                        default => $state,
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label(__('filament.filters.leave_status'))
                    ->options([
                        'pending' => __('filament.status.pending'),
                        'approved_by_head' => __('filament.status.approved_by_head'),
                        'approved_by_hr' => __('filament.status.approved_by_hr'),
                        'rejected' => __('filament.status.rejected'),
                    ]),
            ])
            ->actions([
                Tables\Actions\Action::make('approve_hr')
                    ->label(__('filament.actions.final_approval'))
                    ->color('success')
                    ->icon('heroicon-o-check-circle')
                    ->requiresConfirmation()
                    ->action(function (Leave $record) {
                        $days = $record->duration_in_days;
                        $employee = $record->employee;
                        if ($employee && $employee->remaining_leave_balance < $days) {
                            \Filament\Notifications\Notification::make()
                                ->title(__('filament.notifications.insufficient_leave_balance'))
                                ->danger()
                                ->send();
                            return;
                        }
                        $record->update(['status' => 'approved_by_hr']);
                        if ($employee) {
                            $employee->increment('used_leave_days', $days);
                        }
                        if ($record->employee?->user) {
                            $record->employee->user->notify(new LeaveStatusNotification('approved_by_hr', $record->type));
                        }
                    })
                    ->visible(fn (Leave $record) => $record->status === 'approved_by_head'),
                Tables\Actions\Action::make('reject')
                    ->label(__('filament.actions.reject'))
                    ->color('danger')
                    ->icon('heroicon-o-x-circle')
                    ->requiresConfirmation()
                    ->action(function (Leave $record) {
                        $record->update(['status' => 'rejected']);
                        if ($record->employee?->user) {
                            $record->employee->user->notify(new LeaveStatusNotification('rejected', $record->type));
                        }
                    })
                    ->visible(fn (Leave $record) => in_array($record->status, ['pending', 'approved_by_head'])),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array { return []; }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLeaves::route('/'),
            'edit' => Pages\EditLeave::route('/{record}/edit'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['employee.user.name'];
    }
}
