<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ExpenseResource\Pages;
use App\Filament\Resources\ExpenseResource\RelationManagers;
use App\Models\Expense;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ExpenseResource extends Resource
{
    protected static ?string $model = Expense::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('بيانات المصروف')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->label('سُجل بواسطة')
                            ->relationship('user', 'name')
                            ->default(auth()->id())
                            ->required(),
                        Forms\Components\TextInput::make('title')
                            ->label('عنوان المصروف')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Select::make('category')
                            ->label('التصنيف')
                            ->options([
                                'رواتب' => 'رواتب',
                                'تشغيل' => 'مصاريف تشغيلية',
                                'أدوات' => 'أدوات وبرمجيات',
                                'تسويق' => 'تسويق وإعلانات',
                                'أخرى' => 'أخرى',
                            ])
                            ->searchable()
                            ->required(),
                        Forms\Components\TextInput::make('amount')
                            ->label('المبلغ')
                            ->numeric()
                            ->prefix('$')
                            ->required(),
                        Forms\Components\DatePicker::make('expense_date')
                            ->label('تاريخ المصروف')
                            ->default(now()),
                        Forms\Components\FileUpload::make('receipt_url')
                            ->label('صورة الإيصال / الفاتورة')
                            ->directory('receipts')
                            ->image()
                            ->columnSpanFull(),
                    ])->columns(2),
            ]);
    }


    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('البيان')
                    ->searchable(),
                Tables\Columns\TextColumn::make('category')
                    ->label('التصنيف')
                    ->badge()
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('amount')
                    ->label('المبلغ')
                    ->money('usd')
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('سُجل بواسطة')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('expense_date')
                    ->label('التاريخ')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('receipt_url')
                    ->searchable(),
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
                Tables\Filters\SelectFilter::make('category')
                    ->label('تصفية بالتصنيف')
                    ->options([
                        'رواتب' => 'رواتب',
                        'تشغيل' => 'مصاريف تشغيلية',
                        'أدوات' => 'أدوات وبرمجيات',
                        'تسويق' => 'تسويق وإعلانات',
                        'أخرى' => 'أخرى',
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
            'index' => Pages\ListExpenses::route('/'),
            'create' => Pages\CreateExpense::route('/create'),
            'edit' => Pages\EditExpense::route('/{record}/edit'),
        ];
    }
}
