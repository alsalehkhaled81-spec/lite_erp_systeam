<?php

namespace App\Filament\Resources;

use App\Filament\Accountant\Resources\ClientResource\RelationManagers\InvoicesRelationManager;
use App\Filament\Resources\ClientResource\Pages;
use App\Models\Client;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ClientResource extends Resource
{
    protected static ?string $model = Client::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationGroup = null;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('filament.sections.client_basic_data'))
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label(__('filament.fields.client_name'))
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('company_name')
                            ->label(__('filament.fields.company_name_optional'))
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
                            ->tel()
                            ->numeric(),
                        Forms\Components\Textarea::make('address')
                            ->label(__('filament.fields.address'))
                            ->columnSpanFull(),
                    ])->columns(2),

                Forms\Components\Section::make(__('filament.client_portal.portal_access'))
                    ->description(__('filament.client_portal.portal_access_desc'))
                    ->schema([
                        Forms\Components\TextInput::make('password')
                            ->label(__('filament.fields.password'))
                            ->password()->revealable()
                            ->dehydrated(fn ($state) => filled($state))
                            ->revealable()
                            ->helperText(__('filament.client_portal.password_helper')),
                        Forms\Components\Toggle::make('is_active')
                            ->label(__('filament.client_portal.is_active'))
                            ->default(true),
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
                Tables\Columns\IconColumn::make('is_active')
                    ->label(__('filament.client_portal.is_active'))
                    ->boolean(),
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

    public static function getModelLabel(): string
    {
        return __('filament.model.client');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament.model.clients');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('filament.group.finance');
    }

    public static function getNavigationLabel(): string
    {
        return __('filament.nav.clients');
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'email'];
    }
}
