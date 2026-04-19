<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Resources\RoleResource\Pages;
use App\Filament\Resources\RoleResource\RelationManagers;
use App\Models\Role;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class RoleResource extends Resource
{
    protected static ?string $model = Role::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

public static function form(Form $form): Form
{
    return $form
        ->schema([
            Forms\Components\Section::make('بيانات الصلاحية')
                ->description('إدارة اسم الصلاحية ووصفها')
                ->schema([
                    Forms\Components\TextInput::make('name')
                        ->label('اسم الدور (الرول)')
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->maxLength(255),
                    Forms\Components\Textarea::make('description')
                        ->label('الوصف')
                        ->columnSpanFull(),
                ])->columns(1),
        ]);
}
    public static function table(Table $table): Table
    {
            return $table
        ->columns([
            Tables\Columns\TextColumn::make('name')
                ->label('اسم الدور')
                ->searchable()
                ->sortable()
                ->badge()
                ->color('primary'),
            Tables\Columns\TextColumn::make('description')
                ->label('الوصف')
                ->limit(50)
                ->searchable(),
            Tables\Columns\TextColumn::make('created_at')
                ->label('تاريخ الإنشاء')
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
            'index' => Pages\ListRoles::route('/'),
            'create' => Pages\CreateRole::route('/create'),
            'edit' => Pages\EditRole::route('/{record}/edit'),
        ];
    }
}
