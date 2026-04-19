<?php

namespace App\Filament\Pm\Resources\ProjectResource\RelationManagers;

use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class EmployeesRelationManager extends RelationManager
{
    protected static string $relationship = 'employees';
    protected static ?string $title = 'فريق عمل المشروع';

    public function table(Table $table): Table
    {
        return $table
            // هذه التعليمة السحرية لجلب اسم الموظف من جدول المستخدمين
            ->columns([
                Tables\Columns\TextColumn::make('user.name')->label('اسم الموظف')->searchable(),
                Tables\Columns\TextColumn::make('job_title')->label('المسمى الوظيفي')->badge(),
                ])
                ->recordTitle(fn ($record) => $record->user->name ?? 'موظف')
            ->headerActions([
                Tables\Actions\AttachAction::make()->label('إضافة موظف للمشروع')->preloadRecordSelect(),
            ])
            ->actions([
                Tables\Actions\DetachAction::make()->label('إزالة من المشروع'),
            ]);
    }
}
