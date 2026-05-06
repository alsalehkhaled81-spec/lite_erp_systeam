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
    protected static ?string $title = 'فواتير العميل';

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('project_id')
                ->label('المشروع (اختياري)')
                ->relationship('project', 'name'),
            Forms\Components\TextInput::make('invoice_number')
                ->label('رقم الفاتورة')
                ->required(),
            Forms\Components\TextInput::make('amount')
                ->label('المبلغ')
                ->numeric()
                ->minValue(0)
                ->required()
                ->prefix('$'),
            Forms\Components\Select::make('status')
                ->label('الحالة')
                ->options(['unpaid' => 'غير مدفوعة', 'paid' => 'مدفوعة', 'overdue' => 'متأخرة'])->default('unpaid'),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('invoice_number')->label('رقم الفاتورة'),
                Tables\Columns\TextColumn::make('amount')->label('المبلغ')->money('usd'),
                Tables\Columns\TextColumn::make('status')->label('الحالة')->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'unpaid' => 'warning',
                        'paid' => 'success',
                        'overdue' => 'danger',
                    }),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()->label('إصدار فاتورة'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }
}
