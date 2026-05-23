<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DepartmentResource\Pages;
use App\Models\Department;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class DepartmentResource extends Resource
{
    protected static ?string $model = Department::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';

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
                            ->relationship('head', 'id')
                            ->getOptionLabelFromRecordUsing(fn ($record) => $record->user?->name . ' - ' . $record->job_title)
                            ->searchable()
                            ->nullable()
                            ->getSearchResultsUsing(fn (string $search) => \App\Models\Employee::where('job_title', 'like', "%{$search}%")->orWhereHas('user', fn ($q) => $q->where('name', 'like', "%{$search}%"))->limit(20)->get()->mapWithKeys(fn ($e) => [$e->id => $e->user?->name . ' - ' . $e->job_title])),
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
                    ->default('—'),
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
}
