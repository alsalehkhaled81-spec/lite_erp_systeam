<?php

namespace App\Filament\Hr\Resources\TrainingResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class EmployeesRelationManager extends RelationManager
{
    protected static string $relationship = 'employees';

    public static function getTitle(\Illuminate\Database\Eloquent\Model $ownerRecord, string $panelClass): string
    {
        return __('filament.relation.training_participants');
    }

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('employee_id')
                ->label(__('filament.fields.employee'))
                ->relationship('employees', 'id')
                ->options(\App\Models\Employee::with('user')->get()->pluck('user.name', 'id'))
                ->searchable()
                ->required(),
            Forms\Components\Select::make('status')
                ->label(__('filament.fields.status'))
                ->options([
                    'enrolled' => __('filament.training.enrolled'),
                    'completed' => __('filament.training.completed_status'),
                    'certified' => __('filament.training.certified'),
                ])->default('enrolled'),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label(__('filament.columns.employee_name'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('pivot.status')
                    ->label(__('filament.columns.status'))
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'enrolled' => __('filament.training.enrolled'),
                        'completed' => __('filament.training.completed_status'),
                        'certified' => __('filament.training.certified'),
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('pivot.completion_date')
                    ->label(__('filament.fields.completion_date'))
                    ->date(),
            ])
            ->headerActions([
                Tables\Actions\AttachAction::make()
                    ->label(__('filament.actions.enroll_employee'))
                    ->recordSelect(fn (Forms\Components\Select $select) => $select
                        ->options(
                            \App\Models\Employee::with('user')
                                ->whereDoesntHave('user.role', fn ($q) => $q->where('name', 'super_admin'))
                                ->get()
                                ->pluck('user.name', 'id')
                        )
                        ->searchable()
                        ->getSearchResultsUsing(fn (string $search) => 
                            \App\Models\Employee::with('user')
                                ->whereDoesntHave('user.role', fn ($q) => $q->where('name', 'super_admin'))
                                ->whereHas('user', fn ($q) => $q->where('name', 'like', "%{$search}%"))
                                ->get()
                                ->pluck('user.name', 'id')
                                ->toArray()
                        )
                        ->getOptionLabelUsing(fn ($value) => 
                            \App\Models\Employee::with('user')->find($value)?->user->name ?? 'Unknown'
                        )
                    )
                    ->form(fn (Tables\Actions\AttachAction $action): array => [
                        $action->getRecordSelect(),
                        Forms\Components\Select::make('status')
                            ->label(__('filament.fields.status'))
                            ->options([
                                'enrolled' => __('filament.training.enrolled'),
                                'completed' => __('filament.training.completed_status'),
                                'certified' => __('filament.training.certified'),
                            ])->default('enrolled'),
                    ]),
            ])
            ->actions([
                Tables\Actions\DetachAction::make()->label(__('filament.actions.remove')),
            ]);
    }
}
