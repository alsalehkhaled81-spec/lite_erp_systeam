<?php

namespace App\Filament\Accountant\Resources;

use App\Filament\Accountant\Resources\ClientResource\Pages;
use App\Filament\Accountant\Resources\ClientResource\RelationManagers;
use App\Filament\Accountant\Resources\ClientResource\RelationManagers\InvoicesRelationManager;
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

    public static function getNavigationLabel(): string
    {
        return __('filament.nav.clients');
    }

    public static function getModelLabel(): string
    {
        return __('filament.model.client');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament.model.clients');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('filament.sections.client_basic_data'))
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label(__('filament.fields.client_name_rep'))
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('company_name')
                            ->label(__('filament.fields.company_name'))
                            ->maxLength(255),
                    ])->columns(2),

                Forms\Components\Section::make(__('filament.sections.contact_info'))
                    ->schema([
                        Forms\Components\TextInput::make('email')
                            ->label(__('filament.fields.email'))
                            ->email()
                            ->unique(ignoreRecord: true),
                        Forms\Components\TextInput::make('phone')
                            ->label(__('filament.fields.phone'))
                            ->tel(),
                        Forms\Components\Textarea::make('address')
                            ->label(__('filament.fields.address'))
                            ->columnSpanFull(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(__('filament.columns.name'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('company_name')
                    ->label(__('filament.columns.company'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->label(__('filament.columns.email'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone')
                    ->label(__('filament.columns.phone'))
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
