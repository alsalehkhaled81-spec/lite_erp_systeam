<?php

namespace App\Filament\Employee\Resources;

use App\Filament\Employee\Resources\LeaveResource\Pages;
use App\Models\Leave;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

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
        return __('filament.nav.leaves');
    }

    public static function getModelLabel(): string
    {
        return __('filament.model.leave');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament.model.leaves');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('filament.sections.leave_request'))
                    ->schema([
                        Forms\Components\Select::make('type')
                            ->label(__('filament.fields.leave_type'))
                            ->options([
                                'Sick' => __('filament.leave_type.Sick'),
                                'Annual' => __('filament.leave_type.Annual'),
                                'Emergency' => __('filament.leave_type.Emergency'),
                            ])
                            ->required(),
                        Forms\Components\DatePicker::make('start_date')
                            ->label(__('filament.fields.start_date'))
                            ->required(),
                        Forms\Components\DatePicker::make('end_date')
                            ->label(__('filament.fields.end_date'))
                            ->required(),
                        Forms\Components\Textarea::make('reason')
                            ->label(__('filament.fields.reason'))
                            ->required()
                            ->columnSpanFull(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
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
                    ->date()->sortable(),
                Tables\Columns\TextColumn::make('end_date')
                    ->label(__('filament.columns.end_date'))
                    ->date()->sortable(),
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
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('filament.columns.request_date'))
                    ->dateTime()->sortable(),
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
                Tables\Actions\EditAction::make()
                    ->visible(fn (Leave $record) => $record->status === 'pending'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()]),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->whereHas('employee', function ($query) {
            $query->where('user_id', auth()->id());
        });
    }

    public static function getRelations(): array { return []; }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLeaves::route('/'),
            'create' => Pages\CreateLeave::route('/create'),
            'edit' => Pages\EditLeave::route('/{record}/edit'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['employee.user.name'];
    }
}
