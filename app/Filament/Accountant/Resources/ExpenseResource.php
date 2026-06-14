<?php

namespace App\Filament\Accountant\Resources;

use App\Filament\Accountant\Resources\ExpenseResource\Pages;
use App\Models\Expense;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class ExpenseResource extends Resource
{
    protected static ?string $model = Expense::class;

    protected static ?string $navigationIcon = 'heroicon-o-receipt-percent';
    protected static ?string $navigationGroup = null;
    public static function getNavigationGroup(): ?string
    {
        return __('filament.group.finance');
    }


    public static function getNavigationLabel(): string
    {
        return __('filament.nav.expenses');
    }

    public static function getModelLabel(): string
    {
        return __('filament.model.expense');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament.model.expenses');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('filament.sections.new_expense'))
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->label(__('filament.fields.registered_by'))
                            ->relationship('user', 'name')
                            ->default(fn () => auth()->id())
                            ->disabled()
                            ->dehydrated()
                            ->required(),
                        Forms\Components\Select::make('project_id')
                            ->label(__('filament.fields.project'))
                            ->relationship('project', 'name')
                            ->searchable()
                            ->nullable(),
                        Forms\Components\TextInput::make('title')
                            ->label(__('filament.fields.expense_title'))
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Select::make('category')
                            ->label(__('filament.fields.category'))
                            ->options(__('filament.expense_category'))
                            ->searchable()
                            ->required(),
                        Forms\Components\TextInput::make('amount')
                            ->label(__('filament.fields.amount'))
                            ->numeric()
                            ->minValue(0)
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
                Tables\Columns\TextColumn::make('project.name')
                    ->label(__('filament.columns.project'))
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label(__('filament.columns.registered_by'))
                    ->toggleable(),
                Tables\Columns\TextColumn::make('expense_date')
                    ->label(__('filament.columns.expense_date'))
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label(__('filament.columns.status'))
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'approved' => 'success',
                        'rejected' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => __('filament.status.pending'),
                        'approved' => __('filament.expense.approved'),
                        'rejected' => __('filament.expense.rejected'),
                        default => $state,
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category')
                    ->label(__('filament.filters.filter_by_category'))
                    ->options(__('filament.expense_category')),
                Tables\Filters\SelectFilter::make('status')
                    ->label(__('filament.filters.expense_status'))
                    ->options([
                        'pending' => __('filament.status.pending'),
                        'approved' => __('filament.expense.approved'),
                        'rejected' => __('filament.expense.rejected'),
                    ]),
                Tables\Filters\SelectFilter::make('project_id')
                    ->label(__('filament.filters.filter_by_project'))
                    ->relationship('project', 'name')
                    ->searchable(),
            ])
            ->actions([
                Tables\Actions\Action::make('approve')
                    ->label(__('filament.actions.approve_expense'))
                    ->color('success')
                    ->icon('heroicon-o-check-circle')
                    ->requiresConfirmation()
                    ->action(fn (Expense $record) => $record->update(['status' => 'approved', 'approved_by' => Auth::id()]))
                    ->visible(fn (Expense $record) => $record->status === 'pending'),
                Tables\Actions\Action::make('reject')
                    ->label(__('filament.actions.reject_expense'))
                    ->color('danger')
                    ->icon('heroicon-o-x-circle')
                    ->requiresConfirmation()
                    ->action(fn (Expense $record) => $record->update(['status' => 'rejected', 'approved_by' => Auth::id()]))
                    ->visible(fn (Expense $record) => $record->status === 'pending'),
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
            'index' => Pages\ListExpenses::route('/'),
            'create' => Pages\CreateExpense::route('/create'),
            'edit' => Pages\EditExpense::route('/{record}/edit'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['title'];
    }
}
