<?php

namespace App\Filament\Accountant\Resources\ClientResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class InvoicesRelationManager extends RelationManager
{
    protected static string $relationship = 'invoices';

    public static function getTitle(\Illuminate\Database\Eloquent\Model $ownerRecord, string $pageClass): string
    {
        return __('filament.relation.client_invoices');
    }

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('project_id')
                ->label(__('filament.fields.project_optional'))
                ->relationship('project', 'name'),
            Forms\Components\TextInput::make('invoice_number')
                ->label(__('filament.fields.invoice_number'))
                ->required(),
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
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('invoice_number')->label(__('filament.columns.invoice_number')),
                Tables\Columns\TextColumn::make('amount')->label(__('filament.columns.amount'))->money('usd'),
                Tables\Columns\TextColumn::make('status')->label(__('filament.columns.status'))->badge()
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
            ->headerActions([
                Tables\Actions\CreateAction::make()->label(__('filament.actions.create_invoice')),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }
}
