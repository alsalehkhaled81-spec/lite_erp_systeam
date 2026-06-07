<?php

namespace App\Filament\Accountant\Resources;

use App\Filament\Accountant\Resources\InvoiceResource\Pages;
use App\Models\Invoice;
use App\Services\InvoicePdfService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class InvoiceResource extends Resource
{
    protected static ?string $model = Invoice::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-currency-dollar';
    protected static ?string $navigationGroup = 'المالية';

    public static function getNavigationLabel(): string
    {
        return __('filament.nav.invoices');
    }

    public static function getModelLabel(): string
    {
        return __('filament.model.invoice');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament.model.invoices');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('filament.sections.associations'))
                    ->schema([
                        Forms\Components\Select::make('client_id')
                            ->label(__('filament.fields.client'))
                            ->relationship('client', 'name')
                            ->required()
                            ->searchable()
                            ->live()
                            ->afterStateUpdated(fn (Forms\Set $set) => $set('project_id', null)),
                        Forms\Components\Select::make('project_id')
                            ->label(__('filament.fields.project_optional'))
                            ->relationship('project', 'name', modifyQueryUsing: fn (Builder $query, Forms\Get $get) =>
                                $query->when($get('client_id'), fn ($q, $client) => $q->where('client_id', $client))
                            )
                            ->searchable()
                            ->preload(),
                    ])->columns(2),

                Forms\Components\Section::make(__('filament.sections.invoice_data'))
                    ->schema([
                        Forms\Components\TextInput::make('invoice_number')
                            ->label(__('filament.fields.invoice_number'))
                            ->required()
                            ->unique(ignoreRecord: true),
                        Forms\Components\Select::make('status')
                            ->label(__('filament.fields.status'))
                            ->options([
                                'unpaid' => __('filament.status.unpaid'),
                                'paid' => __('filament.status.paid'),
                                'overdue' => __('filament.status.overdue'),
                            ])->default('unpaid'),
                        Forms\Components\DatePicker::make('issue_date')
                            ->label(__('filament.fields.issue_date')),
                        Forms\Components\DatePicker::make('due_date')
                            ->label(__('filament.fields.due_date_invoice')),
                    ])->columns(2),

                Forms\Components\Section::make(__('filament.sections.invoice_items'))
                    ->schema([
                        Forms\Components\Repeater::make('items')
                            ->label(__('filament.fields.invoice_items'))
                            ->relationship()
                            ->schema([
                                Forms\Components\TextInput::make('description')
                                    ->label(__('filament.fields.item_description'))
                                    ->required()
                                    ->columnSpan(2),
                                Forms\Components\TextInput::make('quantity')
                                    ->label(__('filament.fields.quantity'))
                                    ->numeric()
                                    ->default(1)
                                    ->minValue(0.01)
                                    ->required()
                                    ->live(debounce: 500)
                                    ->afterStateUpdated(fn (Forms\Set $set, Forms\Get $get) => $set('total', round((float)($get('quantity') ?? 0) * (float)($get('unit_price') ?? 0), 2))),
                                Forms\Components\TextInput::make('unit_price')
                                    ->label(__('filament.fields.unit_price'))
                                    ->numeric()
                                    ->prefix('$')
                                    ->minValue(0)
                                    ->required()
                                    ->live(debounce: 500)
                                    ->afterStateUpdated(fn (Forms\Set $set, Forms\Get $get) => $set('total', round((float)($get('quantity') ?? 0) * (float)($get('unit_price') ?? 0), 2))),
                                Forms\Components\TextInput::make('total')
                                    ->label(__('filament.fields.total'))
                                    ->numeric()
                                    ->prefix('$')
                                    ->disabled()
                                    ->dehydrated(),
                            ])->columns(5)
                            ->reorderable()
                            ->afterStateUpdated(function (Forms\Set $set, Forms\Get $get) {
                                $items = $get('items') ?? [];
                                $subtotal = collect($items)->sum(fn ($item) => (float)($item['total'] ?? 0));
                                $vatRate = (float)($get('vat_rate') ?? 0);
                                $set('amount', round($subtotal, 2));
                                $set('vat_amount', round($subtotal * ($vatRate / 100), 2));
                                $set('total_with_vat', round($subtotal + ($subtotal * ($vatRate / 100)), 2));
                            }),
                    ]),

                Forms\Components\Section::make(__('filament.sections.invoice_totals'))
                    ->schema([
                        Forms\Components\TextInput::make('amount')
                            ->label(__('filament.fields.subtotal'))
                            ->numeric()
                            ->prefix('$')
                            ->dehydrated(),
                        Forms\Components\TextInput::make('vat_rate')
                            ->label(__('filament.fields.vat_rate'))
                            ->numeric()
                            ->suffix('%')
                            ->default(0)
                            ->live(debounce: 500)
                            ->afterStateUpdated(function (Forms\Set $set, Forms\Get $get) {
                                $subtotal = (float)($get('amount') ?? 0);
                                $rate = (float)($get('vat_rate') ?? 0);
                                $vat = round($subtotal * ($rate / 100), 2);
                                $set('vat_amount', $vat);
                                $set('total_with_vat', round($subtotal + $vat, 2));
                            }),
                        Forms\Components\TextInput::make('vat_amount')
                            ->label(__('filament.fields.vat_amount'))
                            ->numeric()
                            ->prefix('$')
                            ->disabled()
                            ->dehydrated(),
                        Forms\Components\TextInput::make('total_with_vat')
                            ->label(__('filament.fields.total_with_vat'))
                            ->numeric()
                            ->prefix('$')
                            ->disabled()
                            ->dehydrated(),
                    ])->columns(4),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('client.name')
                    ->label(__('filament.columns.client'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('invoice_number')
                    ->label(__('filament.columns.invoice_number'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('amount')
                    ->label(__('filament.columns.subtotal'))
                    ->money('usd')
                    ->sortable(),
                Tables\Columns\TextColumn::make('vat_amount')
                    ->label(__('filament.columns.vat_amount'))
                    ->money('usd')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('total_with_vat')
                    ->label(__('filament.columns.total_with_vat'))
                    ->money('usd')
                    ->sortable(),
                Tables\Columns\TextColumn::make('issue_date')
                    ->label(__('filament.columns.issue_date'))
                    ->date(),
                Tables\Columns\TextColumn::make('due_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label(__('filament.columns.status'))
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'unpaid' => 'warning',
                        'paid' => 'success',
                        'overdue' => 'danger',
                    })
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'unpaid' => __('filament.status.unpaid'),
                        'paid' => __('filament.status.paid'),
                        'overdue' => __('filament.status.overdue'),
                        default => $state,
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label(__('filament.filters.invoice_status'))
                    ->options([
                        'unpaid' => __('filament.status.unpaid'),
                        'paid' => __('filament.status.paid'),
                        'overdue' => __('filament.status.overdue'),
                    ]),
            ])
            ->actions([
                Tables\Actions\Action::make('download_pdf')
                    ->label(__('filament.actions.download_invoice_pdf'))
                    ->icon('heroicon-o-document-arrow-down')
                    ->action(fn (Invoice $record) => app(InvoicePdfService::class)->generate($record)),
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
            'index' => Pages\ListInvoices::route('/'),
            'create' => Pages\CreateInvoice::route('/create'),
            'edit' => Pages\EditInvoice::route('/{record}/edit'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['invoice_number'];
    }
}
