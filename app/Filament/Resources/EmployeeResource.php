<?php

namespace App\Filament\Resources;

use App\Filament\Hr\Resources\EmployeeResource\RelationManagers\SkillsRelationManager;
use App\Filament\Resources\EmployeeResource\Pages;
use App\Filament\Resources\EmployeeResource\RelationManagers;
use App\Models\Employee;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class EmployeeResource extends Resource
{
    protected static ?string $model = Employee::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
{
    return $form
        ->schema([
            Forms\Components\Section::make('البيانات الوظيفية')
                ->schema([
                    Forms\Components\Select::make('user_id')
                        ->label('حساب المستخدم')
                        ->relationship('user', 'name')
                        ->required(),
                    Forms\Components\TextInput::make('job_title')
                        ->label('المسمى الوظيفي')
                        ->maxLength(255),
                    Forms\Components\TextInput::make('salary')
                        ->label('الراتب')
                        ->numeric()
                        ->minValue(0) // منع القيمة السالبة
                        ->prefix('$'),
                    Forms\Components\Select::make('status')
                        ->label('حالة الموظف')
                        ->options([
                            'active' => 'على رأس العمل',
                            'on_leave' => 'في إجازة',
                            'terminated' => 'مفصول',
                        ])
                        ->default('active')
                        ->required(),
                    Forms\Components\DatePicker::make('hire_date')
                        ->label('تاريخ التعيين'),
                ])->columns(2),
        ]);
}

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                ->label('اسم الموظف')
                ->searchable()
                ->sortable(),
            Tables\Columns\TextColumn::make('job_title')
                ->label('المسمى الوظيفي')
                ->searchable(),
            Tables\Columns\TextColumn::make('salary')
                ->label('الراتب')
                ->money('usd')
                ->sortable(),
            Tables\Columns\TextColumn::make('status')
                ->label('الحالة')
                ->badge()
                ->color(fn (string $state): string => match ($state) {
                    'active' => 'success',
                    'on_leave' => 'warning',
                    'terminated' => 'danger',
                }),
            Tables\Columns\TextColumn::make('hire_date')
                ->label('تاريخ التعيين')
                ->date()
                ->sortable(),

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
                Tables\Filters\SelectFilter::make('status')
                ->label('تصفية حسب الحالة')
                ->options([
                    'active' => 'على رأس العمل',
                    'on_leave' => 'في إجازة',
                    'terminated' => 'مفصول',
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
            SkillsRelationManager::class,

        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEmployees::route('/'),
            'create' => Pages\CreateEmployee::route('/create'),
            'edit' => Pages\EditEmployee::route('/{record}/edit'),
        ];
    }
}
