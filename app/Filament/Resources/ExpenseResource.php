<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ExpenseResource\Pages;
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
                Forms\Components\Section::make(__('filament.sections.expense_data'))
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->label(__('filament.fields.registered_by'))
                            ->relationship('user', 'name')
                            ->required(),
                        Forms\Components\TextInput::make('title')
                            ->label(__('filament.fields.expense_title'))
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Select::make('category')
                            ->label(__('filament.fields.category'))
                            ->options([
                                'salaries' => __('filament.expense_category.salaries'),
                                'operations' => __('filament.expense_category.operations'),
                                'tools' => __('filament.expense_category.tools'),
                                'marketing' => __('filament.expense_category.marketing'),
                                'other' => __('filament.expense_category.other'),
                            ])
                            ->searchable()
                            ->required(),
                        Forms\Components\TextInput::make('amount')
                            ->label(__('filament.fields.amount'))
                            ->numeric()
                            ->prefix('$')
                            ->required(),
                        Forms\Components\DatePicker::make('expense_date')
                            ->label(__('filament.fields.expense_date'))
                            ->default(now()),
                        Forms\Components\FileUpload::make('receipt_url')
                            ->label(__('filament.fields.receipt_image'))
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
                    ->label(__('filament.columns.title'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('category')
                    ->label(__('filament.columns.category'))
                    ->badge()
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('amount')
                    ->label(__('filament.columns.amount'))
                    ->money('usd')
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label(__('filament.columns.registered_by'))
                    ->toggleable(),
                Tables\Columns\TextColumn::make('expense_date')
                    ->label(__('filament.columns.expense_date'))
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
                    ->label(__('filament.filters.filter_by_category'))
                    ->options([
                        'salaries' => __('filament.expense_category.salaries'),
                        'operations' => __('filament.expense_category.operations'),
                        'tools' => __('filament.expense_category.tools'),
                        'marketing' => __('filament.expense_category.marketing'),
                        'other' => __('filament.expense_category.other'),
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

    public static function getModelLabel(): string
    {
        return __('filament.model.expense');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament.model.expenses');
    }

    public static function getNavigationLabel(): string
    {
        return __('filament.nav.expenses');
    }
}
