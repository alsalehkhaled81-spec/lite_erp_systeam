<?php

namespace App\Filament\Employee\Resources;

use App\Filament\Employee\Resources\ReportResource\Pages;
use App\Models\Report;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ReportResource extends Resource
{
    protected static ?string $model = Report::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    public static function getNavigationLabel(): string
    {
        return __('filament.nav.reports');
    }

    public static function getModelLabel(): string
    {
        return __('filament.model.report');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament.model.reports');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('filament.sections.report_data'))
                    ->schema([
                        Forms\Components\Select::make('receiver_id')
                            ->label(__('filament.fields.receiver'))
                            ->relationship('receiver', 'job_title')
                            ->searchable()
                            ->getSearchResultsUsing(fn (string $search) => \App\Models\Employee::where('job_title', 'like', "%{$search}%")->orWhereHas('user', fn ($q) => $q->where('name', 'like', "%{$search}%"))->limit(20)->get()->mapWithKeys(fn ($e) => [$e->id => $e->user?->name . ' - ' . $e->job_title]))
                            ->required(),
                        Forms\Components\TextInput::make('title')
                            ->label(__('filament.fields.report_title'))
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Textarea::make('content')
                            ->label(__('filament.fields.content'))
                            ->required()
                            ->columnSpanFull(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label(__('filament.columns.title'))
                    ->searchable()
                    ->limit(30),
                Tables\Columns\TextColumn::make('receiver.user.name')
                    ->label(__('filament.columns.receiver'))
                    ->searchable(),
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
                Tables\Columns\TextColumn::make('feedback')
                    ->label(__('filament.fields.feedback'))
                    ->limit(30)
                    ->default('—'),
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
        return parent::getEloquentQuery()->whereHas('sender', function ($query) {
            $query->where('user_id', auth()->id());
        });
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
            'index' => Pages\ListReports::route('/'),
            'create' => Pages\CreateReport::route('/create'),
            'edit' => Pages\EditReport::route('/{record}/edit'),
        ];
    }
}