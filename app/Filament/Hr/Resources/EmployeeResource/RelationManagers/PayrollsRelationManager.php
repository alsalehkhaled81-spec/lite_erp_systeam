<?php

namespace App\Filament\Hr\Resources\EmployeeResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class PayrollsRelationManager extends RelationManager
{
    protected static string $relationship = 'payrolls';

    public static function getTitle(\Illuminate\Database\Eloquent\Model $ownerRecord, string $panelClass): string
    {
        return __('filament.relation.employee_payrolls');
    }

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('month_year')
                ->label(__('filament.fields.month'))
                ->type('month')
                ->required(),
            Forms\Components\TextInput::make('basic_salary')
                ->label(__('filament.fields.basic_salary'))
                ->numeric()->prefix('$')->required(),
            Forms\Components\TextInput::make('bonuses')
                ->label(__('filament.fields.bonuses'))
                ->numeric()->prefix('$')->default(0),
            Forms\Components\TextInput::make('deductions')
                ->label(__('filament.fields.deductions'))
                ->numeric()->prefix('$')->default(0),
            Forms\Components\TextInput::make('net_salary')
                ->label(__('filament.fields.net_salary'))
                ->numeric()->prefix('$')->required(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('month_year')
                    ->label(__('filament.columns.month'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('basic_salary')
                    ->label(__('filament.columns.basic_salary'))
                    ->money('usd'),
                Tables\Columns\TextColumn::make('bonuses')
                    ->label(__('filament.columns.bonuses'))
                    ->money('usd'),
                Tables\Columns\TextColumn::make('deductions')
                    ->label(__('filament.columns.deductions'))
                    ->money('usd'),
                Tables\Columns\TextColumn::make('net_salary')
                    ->label(__('filament.columns.net_salary'))
                    ->money('usd'),
                Tables\Columns\TextColumn::make('status')
                    ->label(__('filament.columns.status'))
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'paid' => 'success',
                        'unpaid' => 'warning',
                        default => 'gray',
                    }),
            ])
            ->headerActions([Tables\Actions\CreateAction::make()])
            ->actions([Tables\Actions\EditAction::make(), Tables\Actions\DeleteAction::make()])
            ->defaultSort('month_year', 'desc');
    }
}
