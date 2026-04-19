<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Accountant\Resources\ClientResource\RelationManagers\InvoicesRelationManager;
use App\Filament\Resources\ClientResource\Pages;
use App\Filament\Resources\ClientResource\RelationManagers;
use App\Models\Client;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ClientResource extends Resource
{
    protected static ?string $model = Client::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
{
    return $form
        ->schema([
            Forms\Components\Section::make('البيانات الأساسية للعميل')
                ->schema([
                    Forms\Components\TextInput::make('name')
                        ->label('اسم العميل')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('company_name')
                        ->label('اسم الشركة (إن وجد)')
                        ->maxLength(255),
                ])->columns(2),

            Forms\Components\Section::make('معلومات التواصل')
                ->schema([
                    Forms\Components\TextInput::make('email')
                        ->label('البريد الإلكتروني')
                        ->email()
                        ->unique(ignoreRecord: true),
                    Forms\Components\TextInput::make('phone')
                        ->label('رقم الهاتف')
                        ->tel(),
                    Forms\Components\Textarea::make('address')
                        ->label('العنوان')
                        ->columnSpanFull(),
                ])->columns(2),
        ]);
}


    public static function table(Table $table): Table
    {
        return $table
            ->columns([
            Tables\Columns\TextColumn::make('name')
                ->label('الاسم')
                ->searchable()
                ->sortable(),
            Tables\Columns\TextColumn::make('company_name')
                ->label('الشركة')
                ->searchable(),
            Tables\Columns\TextColumn::make('email')
                ->label('البريد الإلكتروني')
                ->searchable(),
            Tables\Columns\TextColumn::make('phone')
                ->label('الهاتف')
                ->searchable(),
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
                    InvoicesRelationManager::class,

        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListClients::route('/'),
            'create' => Pages\CreateClient::route('/create'),
            'edit' => Pages\EditClient::route('/{record}/edit'),
        ];
    }
}
