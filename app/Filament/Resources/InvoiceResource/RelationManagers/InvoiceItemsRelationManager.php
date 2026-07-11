<?php

namespace App\Filament\Resources\InvoiceResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class InvoiceItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'items';

    public static function getTitle(\Illuminate\Database\Eloquent\Model $ownerRecord, string $panelClass): string
    {
        return __('filament.relation.invoice_items');
    }

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('description')
                ->label(__('filament.fields.item_description'))
                ->required()
                ->maxLength(255),
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
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('description')
                    ->label(__('filament.fields.item_description'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('quantity')
                    ->label(__('filament.fields.quantity'))
                    ->numeric(),
                Tables\Columns\TextColumn::make('unit_price')
                    ->label(__('filament.fields.unit_price'))
                    ->money('usd'),
                Tables\Columns\TextColumn::make('total')
                    ->label(__('filament.fields.total'))
                    ->money('usd')
                    ->sortable(),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
