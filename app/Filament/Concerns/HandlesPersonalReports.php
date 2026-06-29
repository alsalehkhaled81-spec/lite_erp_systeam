<?php

namespace App\Filament\Concerns;

use App\Models\Employee;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

trait HandlesPersonalReports
{
    public static function personalEmployeeId(): ?int
    {
        return Employee::where('user_id', auth()->id())->value('id');
    }

    public static function allowedReceiversQuery(): Builder
    {
        $employeeId = self::personalEmployeeId();
        
        return Employee::with('user')
            ->whereKeyNot($employeeId)
            ->whereHas('user', function ($query) {
                $query->where('is_approved', true)
                      ->whereHas('role', function ($roleQuery) {
                          $roleQuery->whereNotIn('name', ['super_admin', 'hr_manager', 'accountant']);
                      });
            });
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('filament.sections.report_data'))
                    ->schema([
                        Forms\Components\Select::make('sender_id')
                            ->label(__('filament.fields.sender') ?? 'المرسل')
                            ->options(function () {
                                return Employee::with('user')->get()->mapWithKeys(fn ($e) => [$e->id => $e->user?->name . ' - ' . $e->job_title]);
                            })
                            ->searchable()
                            ->getSearchResultsUsing(fn (string $search) => Employee::whereHas('user', fn ($q) => $q->where('name', 'like', "%{$search}%"))->orWhere('job_title', 'like', "%{$search}%")->limit(20)->get()->mapWithKeys(fn ($e) => [$e->id => $e->user?->name . ' - ' . $e->job_title]))
                            ->default(fn () => self::personalEmployeeId())
                            ->disabled(fn () => auth()->user()?->role?->name !== 'super_admin')
                            ->dehydrated()
                            ->required(),
                        Forms\Components\Select::make('receiver_id')
                            ->label(__('filament.fields.receiver'))
                            ->options(function () {
                                return self::allowedReceiversQuery()
                                    ->get()
                                    ->mapWithKeys(fn ($e) => [$e->id => $e->user?->name . ' - ' . $e->job_title]);
                            })
                            ->searchable()
                            ->getSearchResultsUsing(fn (string $search) => self::allowedReceiversQuery()->where(function ($q) use ($search) {
                                $q->whereHas('user', fn ($uq) => $uq->where('name', 'like', "%{$search}%"))
                                  ->orWhere('job_title', 'like', "%{$search}%");
                            })->limit(20)->get()->mapWithKeys(fn ($e) => [$e->id => $e->user?->name . ' - ' . $e->job_title]))
                            ->required()
                            ->disabledOn('edit'),
                        Forms\Components\TextInput::make('title')
                            ->label(__('filament.fields.report_title'))
                            ->required()
                            ->disabledOn('edit')
                            ->maxLength(255),
                        Forms\Components\Textarea::make('content')
                            ->label(__('filament.fields.content'))
                            ->required()
                            ->disabledOn('edit')
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('feedback')
                            ->label(__('filament.fields.feedback'))
                            ->visible(fn (?Forms\Get $get, $record): bool => $record !== null && $record->receiver_id === self::personalEmployeeId())
                            ->columnSpanFull(),
                        Forms\Components\Hidden::make('status')
                            ->default('unread'),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label(__('filament.columns.report_title'))
                    ->searchable()
                    ->limit(30),
                Tables\Columns\TextColumn::make('counterparty')
                    ->label(__('filament.columns.counterparty'))
                    ->searchable(['sender_id', 'receiver_id'])
                    ->state(function ($record) {
                        $personalId = self::personalEmployeeId();
                        if ($record->sender_id === $personalId) {
                            return '← ' . ($record->receiver?->user?->name ?? '—');
                        } elseif ($record->receiver_id === $personalId) {
                            return '→ ' . ($record->sender?->user?->name ?? '—');
                        }
                        return ($record->sender?->user?->name ?? '—') . ' → ' . ($record->receiver?->user?->name ?? '—');
                    }),
                Tables\Columns\TextColumn::make('status')
                    ->label(__('filament.columns.status'))
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'unread' => 'warning',
                        'read' => 'info',
                        'replied' => 'success',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'unread' => __('filament.status.unread'),
                        'read' => __('filament.status.read'),
                        'replied' => __('filament.status.replied'),
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('filament.columns.sent_date'))
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label(__('filament.filters.report_status'))
                    ->options([
                        'unread' => __('filament.status.unread'),
                        'read' => __('filament.status.read'),
                        'replied' => __('filament.status.replied'),
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->visible(fn ($record): bool => $record->receiver_id === self::personalEmployeeId())
                    ->after(function ($record) {
                        if ($record->status === 'unread') {
                            $record->update(['status' => 'read']);
                        }
                    }),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        $employeeId = self::personalEmployeeId();
        $user = auth()->user();

        if ($user && $user->role && $user->role->name === 'super_admin') {
            return parent::getEloquentQuery();
        }

        return parent::getEloquentQuery()->where(function (Builder $query) use ($employeeId) {
            $query->where('sender_id', $employeeId)
                ->orWhere('receiver_id', $employeeId);
        });
    }
}
