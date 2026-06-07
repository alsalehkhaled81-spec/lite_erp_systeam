<?php

namespace App\Filament\Hr\Resources;

use App\Filament\Hr\Resources\DepartmentResource\Pages;
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

    protected static ?string $navigationGroup = 'إدارة الموظفين';

    protected static ?string $navigationLabel = 'الأقسام';

    protected static ?string $modelLabel = 'قسم';

    protected static ?string $pluralModelLabel = 'الأقسام';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('بيانات القسم')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('اسم القسم')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Select::make('head_id')
                            ->label('رئيس القسم')
                            ->relationship('head', 'job_title')
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
                    ->label('اسم القسم')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('head.user.name')
                    ->label('رئيس القسم')
                    ->searchable()
                    ->default('—'),
                Tables\Columns\TextColumn::make('employees_count')
                    ->label('عدد الموظفين')
                    ->counts('employees')
                    ->sortable(),
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

    public static function getModelLabel(): string
    {
        return __('filament.model.department');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament.model.departments');
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
