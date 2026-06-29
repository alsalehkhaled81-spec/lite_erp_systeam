<?php

namespace App\Filament\Client\Resources;

use App\Filament\Client\Resources\InvoiceResource\Pages;
use App\Models\Invoice;
use App\Services\InvoicePdfService;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class InvoiceResource extends Resource
{
    protected static ?string $model = Invoice::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = null;

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make(__('filament.sections.invoice_data'))
                    ->schema([
                        Infolists\Components\TextEntry::make('invoice_number')
                            ->label(__('filament.fields.invoice_number')),
                        Infolists\Components\TextEntry::make('project.name')
                            ->label(__('filament.fields.project')),
                        Infolists\Components\TextEntry::make('issue_date')
                            ->label(__('filament.fields.issue_date'))
                            ->date(),
                        Infolists\Components\TextEntry::make('due_date')
                            ->label(__('filament.fields.due_date_invoice'))
                            ->date(),
                        Infolists\Components\TextEntry::make('status')
                            ->label(__('filament.fields.status'))
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'paid' => 'success',
                                'unpaid' => 'warning',
                                'overdue' => 'danger',
                                default => 'gray',
                            }),
                    ])->columns(2),
                Infolists\Components\Section::make(__('filament.sections.invoice_totals'))
                    ->schema([
                        Infolists\Components\TextEntry::make('amount')
                            ->label(__('filament.fields.amount'))
                            ->money('usd'),
                        Infolists\Components\TextEntry::make('vat_rate')
                            ->label(__('filament.fields.vat_rate'))
                            ->formatStateUsing(fn ($state) => $state.'%'),
                        Infolists\Components\TextEntry::make('vat_amount')
                            ->label(__('filament.columns.vat_amount'))
                            ->money('usd'),
                        Infolists\Components\TextEntry::make('total_with_vat')
                            ->label(__('filament.columns.total_with_vat'))
                            ->money('usd'),
                    ])->columns(2),
                Infolists\Components\Section::make(__('filament.sections.invoice_items'))
                    ->schema([
                        Infolists\Components\RepeatableEntry::make('items')
                            ->schema([
                                Infolists\Components\TextEntry::make('description')
                                    ->label(__('filament.fields.item_description')),
                                Infolists\Components\TextEntry::make('quantity')
                                    ->label(__('filament.fields.quantity')),
                                Infolists\Components\TextEntry::make('unit_price')
                                    ->label(__('filament.fields.unit_price'))
                                    ->money('usd'),
                                Infolists\Components\TextEntry::make('total')
                                    ->label(__('filament.fields.subtotal'))
                                    ->money('usd'),
                            ])
                            ->columns(4),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('invoice_number')
                    ->label(__('filament.columns.invoice_number'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('project.name')
                    ->label(__('filament.columns.project')),
                Tables\Columns\TextColumn::make('amount')
                    ->label(__('filament.columns.amount'))
                    ->money('usd')
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_with_vat')
                    ->label(__('filament.columns.total_with_vat'))
                    ->money('usd')
                    ->sortable(),
                Tables\Columns\TextColumn::make('issue_date')
                    ->label(__('filament.columns.issue_date'))
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('due_date')
                    ->label(__('filament.fields.due_date_invoice'))
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label(__('filament.columns.status'))
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'paid' => 'success',
                        'unpaid' => 'warning',
                        'overdue' => 'danger',
                        default => 'gray',
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label(__('filament.filters.invoice_status'))
                    ->options([
                        'paid' => __('filament.status.paid'),
                        'unpaid' => __('filament.status.unpaid'),
                        'overdue' => __('filament.status.overdue'),
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\Action::make('download_pdf')
                    ->label(__('filament.actions.download_invoice_pdf'))
                    ->icon('heroicon-o-document-arrow-down')
                    ->action(fn (Invoice $record) => app(InvoicePdfService::class)->generate($record)),
            ])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListInvoices::route('/'),
            'view' => Pages\ViewInvoice::route('/{record}'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('client_id', auth('client')->id());
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit(Model $record): bool
    {
        return false;
    }

    public static function canDelete(Model $record): bool
    {
        return false;
    }

    public static function getModelLabel(): string
    {
        return __('filament.model.invoice');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament.model.invoices');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('filament.client_portal.my_projects');
    }

    public static function getNavigationLabel(): string
    {
        return __('filament.nav.invoices');
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['invoice_number'];
    }
}
