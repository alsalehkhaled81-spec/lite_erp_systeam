<?php

namespace App\Filament\Hr\Resources;

use App\Filament\Hr\Resources\ResumeResource\Pages;
use App\Filament\Hr\Resources\ResumeResource\RelationManagers;
use App\Models\Resume;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ResumeResource extends Resource
{
    protected static ?string $model = Resume::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
{
    return $form
        ->schema([
            Forms\Components\Section::make('ملف السيرة الذاتية')
                ->schema([
                    Forms\Components\Select::make('employee_id')
                        ->label('الموظف')
                        ->relationship('employee.user', 'name')
                        ->required()
                        ->searchable(),
                    Forms\Components\FileUpload::make('file_path')
                        ->label('ملف الـ PDF / Word')
                        ->directory('resumes')
                        ->acceptedFileTypes(['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'])
                        ->preserveFilenames(),
                ])->columns(2),

            Forms\Components\Section::make('النص المستخرج (AI-ATS)')
                ->description('هذا النص يقرأه الذكاء الاصطناعي لفرز المهارات')
                ->schema([
                    Forms\Components\Textarea::make('resume_text')
                        ->label('النص')
                        ->rows(8)
                        ->columnSpanFull(),
                ]),
        ]);
}

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                            Tables\Columns\TextColumn::make('employee.user.name')
                ->label('اسم الموظف')
                ->searchable()
                ->sortable(),
            Tables\Columns\TextColumn::make('file_path')
                ->label('الملف')
                ->url(fn ($record) => $record->file_path ? asset('storage/' . $record->file_path) : null)
                ->openUrlInNewTab()
                ->badge()
                ->color('info')
                ->formatStateUsing(fn () => 'تحميل / عرض'),
            Tables\Columns\TextColumn::make('created_at')
                ->label('تاريخ الرفع')
                ->date()
                ->sortable(),
                Tables\Columns\TextColumn::make('employee.id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('updated_at')
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
            'index' => Pages\ListResumes::route('/'),
            'create' => Pages\CreateResume::route('/create'),
            'edit' => Pages\EditResume::route('/{record}/edit'),
        ];
    }
}
