<?php

namespace App\Filament\Client\Resources\ProjectResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class InvoicesRelationManager extends RelationManager
{
    protected static string $relationship = 'invoices';

    public static function getTitle(Model $ownerRecord, string $panelClass): string
    {
        return __('filament.relation.client_invoices');
    }

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('invoice_number')
                ->label(__('filament.fields.invoice_number')),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('invoice_number')
                    ->label(__('filament.columns.invoice_number')),
                Tables\Columns\TextColumn::make('amount')
                    ->label(__('filament.columns.amount'))
                    ->money('usd'),
                Tables\Columns\TextColumn::make('total_with_vat')
                    ->label(__('filament.columns.total_with_vat'))
                    ->money('usd'),
                Tables\Columns\TextColumn::make('issue_date')
                    ->label(__('filament.columns.issue_date'))
                    ->date(),
                Tables\Columns\TextColumn::make('due_date')
                    ->label(__('filament.fields.due_date_invoice'))
                    ->date(),
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
            ->headerActions([])
            ->actions([])
            ->bulkActions([]);
    }

    public function isReadOnly(): bool
    {
        return true;
    }
}
