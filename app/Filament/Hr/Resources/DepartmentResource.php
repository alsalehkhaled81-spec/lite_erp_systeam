<?php

namespace App\Filament\Hr\Resources;

use App\Filament\Hr\Resources\DepartmentResource\Pages;
use App\Models\Department;
use App\Models\Employee;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class DepartmentResource extends Resource
{
    protected static ?string $model = Department::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';

    protected static ?string $navigationGroup = null;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('filament.sections.department_data'))
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label(__('filament.fields.department_name'))
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Select::make('head_id')
                            ->label(__('filament.fields.department_head'))
                            ->searchable()
                            ->nullable()
                            ->getSearchResultsUsing(function (string $search, $livewire) {
                                $currentHeadId = null;
                                $record = $livewire->getRecord();
                                if ($record) {
                                    $currentHeadId = $record->head_id;
                                }

                                return Employee::eligibleDepartmentHead($currentHeadId)
                                    ->where(function ($q) use ($search) {
                                        $q->where('job_title', 'like', "%{$search}%")
                                            ->orWhereHas('user', fn ($q2) => $q2->where('name', 'like', "%{$search}%"));
                                    })
                                    ->limit(20)
                                    ->get()
                                    ->mapWithKeys(fn ($e) => [$e->id => ($e->user?->name ?? '—') . ' - ' . $e->job_title]);
                            })
                            ->getOptionLabelUsing(function ($value) {
                                $employee = \App\Models\Employee::with('user')->find($value);
                                return $employee ? ($employee->user?->name ?? '—') . ' - ' . $employee->job_title : null;
                            }),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(__('filament.columns.department_name'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('head.user.name')
                    ->label(__('filament.columns.department_head'))
                    ->searchable()
                    ->default('—'),
                Tables\Columns\TextColumn::make('employees_count')
                    ->label(__('filament.columns.employees_count'))
                    ->counts('employees')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ListDepartments::route('/'),
            'create' => Pages\CreateDepartment::route('/create'),
            'edit' => Pages\EditDepartment::route('/{record}/edit'),
        ];
    }

    public static function getModelLabel(): string
    {
        return __('filament.model.department');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament.model.departments');
    }
    public static function getNavigationGroup(): ?string
    {
        return __('filament.group.employee_management');
    }


    public static function getNavigationLabel(): string
    {
        return __('filament.nav.departments');
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name'];
    }
}
