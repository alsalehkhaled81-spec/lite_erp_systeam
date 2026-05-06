<?php

namespace App\Filament\Accountant\Resources;

use App\Filament\Accountant\Resources\InvoiceResource\Pages;
use App\Filament\Accountant\Resources\InvoiceResource\RelationManagers;
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

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('الارتباطات')
                    ->schema([
                        Forms\Components\Select::make('client_id')
                            ->label('العميل')
                            ->relationship('client', 'name')
                            ->required()
                            ->searchable(),
                        Forms\Components\Select::make('project_id')
                            ->label('المشروع (اختياري)')
                            ->relationship('project', 'name')
                            ->searchable(),
                    ])->columns(2),

                Forms\Components\Section::make('بيانات الفاتورة')
                    ->schema([
                        Forms\Components\TextInput::make('invoice_number')
                            ->label('رقم الفاتورة')
                            ->required()
                            ->unique(ignoreRecord: true),
                        Forms\Components\TextInput::make('amount')
                            ->label('المبلغ')
                            ->numeric()
                             ->minValue(0)
                            ->required()
                            ->prefix('$'),
                        Forms\Components\Select::make('status')
                            ->label('الحالة')
                            ->options([
                                'unpaid' => 'غير مدفوعة',
                                'paid' => 'مدفوعة',
                                'overdue' => 'متأخرة',
                            ])->default('unpaid'),
                        Forms\Components\DatePicker::make('issue_date')
                            ->label('تاريخ الإصدار'),
                        Forms\Components\DatePicker::make('due_date')
                            ->label('تاريخ الاستحقاق'),
                    ])->columns(3),
            ]);
    }


    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('client.name')
                    ->label('العميل')
                    ->searchable(),
                Tables\Columns\TextColumn::make('project.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('invoice_number')
                    ->label('رقم الفاتورة')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('amount')
                    ->label('المبلغ')
                    ->money('usd')
                    ->sortable(),
                Tables\Columns\TextColumn::make('issue_date')
                    ->label('تاريخ الإصدار')
                    ->date(),
                Tables\Columns\TextColumn::make('due_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('الحالة')
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
                    ->label('تصفية بالحالة')
                    ->options([
                        'unpaid' => 'غير مدفوعة',
                        'paid' => 'مدفوعة',
                        'overdue' => 'متأخرة',
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
}
