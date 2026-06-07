<?php

namespace App\Filament\Accountant\Widgets;

use App\Models\Invoice;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class OverdueInvoicesAlert extends BaseWidget
{
    protected static ?int $sort = 3;
    protected int | string | array $columnSpan = 'full';

    public function getHeading(): string
    {
        return __('filament.widgets.overdue_invoices');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Invoice::query()
                    ->where(function ($query) {
                        $query->where('status', 'overdue')
                              ->orWhere(function ($q) {
                                  $q->where('status', 'unpaid')
                                    ->whereNotNull('due_date')
                                    ->where('due_date', '<', now());
                              });
                    })
                    ->with('client')
                    ->orderBy('due_date')
            )
            ->columns([
                Tables\Columns\TextColumn::make('invoice_number')
                    ->label(__('filament.fields.invoice_number'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('client.name')
                    ->label(__('filament.columns.client'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('amount')
                    ->label(__('filament.columns.amount'))
                    ->money('usd')
                    ->sortable(),
                Tables\Columns\TextColumn::make('due_date')
                    ->label(__('filament.fields.due_date'))
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('days_overdue')
                    ->label(__('filament.widgets.days_overdue'))
                    ->getStateUsing(fn ($record) => $record->due_date ? now()->diffInDays($record->due_date) : 0)
                    ->badge()
                    ->color('danger'),
                Tables\Columns\TextColumn::make('status')
                    ->label(__('filament.columns.status'))
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'unpaid' => 'warning',
                        'overdue' => 'danger',
                        default => 'gray',
                    }),
            ])
            ->paginated(false)
            ->emptyStateHeading(__('filament.widgets.no_overdue_invoices'))
            ->emptyStateDescription(__('filament.widgets.no_overdue_invoices_desc'))
            ->emptyStateIcon('heroicon-o-check-circle');
    }
}
