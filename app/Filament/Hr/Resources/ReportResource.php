<?php

namespace App\Filament\Hr\Resources;

use App\Filament\Hr\Resources\ReportResource\Pages;
use App\Models\Report;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ReportResource extends Resource
{
    protected static ?string $model = Report::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-chart-bar';

    protected static ?string $navigationGroup = null;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('filament.sections.report_data'))
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label(__('filament.fields.report_title'))
                            ->disabled(),
                        Forms\Components\Textarea::make('content')
                            ->label(__('filament.fields.content'))
                            ->disabled()
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('feedback')
                            ->label(__('filament.fields.feedback'))
                            ->columnSpanFull(),
                        Forms\Components\Select::make('status')
                            ->label(__('filament.fields.status'))
                            ->options([
                                'unread' => __('filament.status.unread'),
                                'read' => __('filament.status.read'),
                                'replied' => __('filament.status.replied'),
                            ])
                            ->required(),
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
                Tables\Columns\TextColumn::make('sender.user.name')
                    ->label(__('filament.columns.sender'))
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
                Tables\Actions\EditAction::make()
                    ->label(__('filament.actions.view_reply')),
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
            'index' => Pages\ListReports::route('/'),
            'edit' => Pages\EditReport::route('/{record}/edit'),
        ];
    }

    public static function getModelLabel(): string
    {
        return __('filament.model.report');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament.model.reports');
    }
    public static function getNavigationGroup(): ?string
    {
        return __('filament.group.reports');
    }


    public static function getNavigationLabel(): string
    {
        return __('filament.nav.reports');
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['title'];
    }
}
