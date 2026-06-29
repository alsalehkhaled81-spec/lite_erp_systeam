<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DepartmentResource\Pages;
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

    public static function getNavigationGroup(): ?string
    {
        return __('filament.group.employee_management');
    }

    public static function getNavigationLabel(): string
    {
        return __('filament.model.departments');
    }

    public static function getModelLabel(): string
    {
        return __('filament.model.department');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament.model.departments');
    }

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
                            ->getOptionLabelUsing(fn ($value): ?string => Employee::with('user')->find($value)?->user?->name),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(__('filament.fields.department_name'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('head.user.name')
                    ->label(__('filament.fields.department_head'))
                    ->searchable()
                    ->default('—')
                    ->placeholder('—'),
                Tables\Columns\TextColumn::make('employees_count')
                    ->label(__('filament.fields.employees_count'))
                    ->counts('employees')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
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
            'index' => Pages\ListDepartments::route('/'),
            'create' => Pages\CreateDepartment::route('/create'),
            'edit' => Pages\EditDepartment::route('/{record}/edit'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name'];
    }
}