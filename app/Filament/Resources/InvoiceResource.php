<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InvoiceResource\Pages;
use App\Models\Invoice;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class InvoiceResource extends Resource
{
    protected static ?string $model = Invoice::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-currency-dollar';

    protected static ?string $navigationGroup = null;

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
                            ->createOptionForm([
                                Forms\Components\Hidden::make('client_id')
                                    ->default(fn (Forms\Get $get) => $get('client_id')),
                                Forms\Components\TextInput::make('name')
                                    ->label(__('filament.fields.project_name'))
                                    ->required(),
                                Forms\Components\Textarea::make('description')
                                    ->label(__('filament.fields.description')),
                            ]),
                    ])->columns(2),

                Forms\Components\Section::make(__('filament.sections.invoice_data'))
                    ->schema([
                        Forms\Components\TextInput::make('invoice_number')
                            ->label(__('filament.fields.invoice_number'))
                            ->required()
                            ->unique(ignoreRecord: true),
                        Forms\Components\TextInput::make('amount')
                            ->label(__('filament.fields.amount'))
                            ->numeric()
                            ->minValue(0)
                            ->required()
                            ->prefix('$'),
                        Forms\Components\Select::make('status')
                            ->label(__('filament.fields.status'))
                            ->options([
                                'unpaid' => __('filament.status.unpaid'),
                                'paid' => __('filament.status.paid'),
                                'overdue' => __('filament.status.overdue'),
                            ])->default('unpaid'),
                        Forms\Components\DatePicker::make('issue_date')
                            ->label(__('filament.fields.issue_date'))
                            ->live(),
                        Forms\Components\DatePicker::make('due_date')
                            ->label(__('filament.fields.due_date_invoice'))
                            ->minDate(fn (Forms\Get $get): ?string => $get('issue_date') ?: null)
                            ->rule('after_or_equal:issue_date')
                            ->validationMessages([
                                'after_or_equal' => __('filament.validation.due_date_after_issue', ['attribute' => __('filament.fields.due_date_invoice')]),
                            ]),
                    ])->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('client.name')
                    ->label(__('filament.columns.client'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('project.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('invoice_number')
                    ->label(__('filament.columns.invoice_number'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('amount')
                    ->label(__('filament.columns.amount'))
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
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
            'index' => Pages\ListInvoices::route('/'),
            'create' => Pages\CreateInvoice::route('/create'),
            'edit' => Pages\EditInvoice::route('/{record}/edit'),
        ];
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
        return __('filament.group.finance');
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
