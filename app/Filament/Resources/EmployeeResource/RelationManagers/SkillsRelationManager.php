<?php
namespace App\Filament\Hr\Resources\EmployeeResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class SkillsRelationManager extends RelationManager
{
    protected static string $relationship = 'skills';
    protected static ?string $title = 'مهارات الموظف';

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')->label('المهارة')->required(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('اسم المهارة')->badge(),
            ])
            ->headerActions([
                Tables\Actions\AttachAction::make()->label('إضافة مهارة للموظف')->preloadRecordSelect(),
                Tables\Actions\CreateAction::make()->label('إنشاء مهارة جديدة'),
            ])
            ->actions([
                Tables\Actions\DetachAction::make()->label('إزالة'),
            ]);
    }
}
