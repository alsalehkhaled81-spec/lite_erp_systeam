<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-shield-check';

    protected static ?string $navigationGroup = null;
    public static function getNavigationGroup(): ?string
    {
        return __('filament.group.system');
    }


    public static function getNavigationLabel(): string
    {
        return __('filament.model.users');
    }

    public static function getModelLabel(): string
    {
        return __('filament.model.user');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament.model.users');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('filament.sections.user_data'))
                    ->schema([
                        Forms\Components\Select::make('role_id')
                            ->label(__('filament.fields.role'))
                            ->relationship('role', 'name')
                            ->required(),
                        Forms\Components\TextInput::make('name')
                            ->label(__('filament.fields.full_name'))
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('email')
                            ->label(__('filament.fields.email'))
                            ->email()
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('password')
                            ->label(__('filament.fields.password'))
                            ->password()
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Toggle::make('is_approved')
                            ->label(__('filament.fields.is_approved')),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(__('filament.columns.name'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->label(__('filament.columns.email'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('role.name')
                    ->label(__('filament.columns.role')),
                Tables\Columns\IconColumn::make('is_approved')
                    ->label(__('filament.fields.is_approved'))
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('role_id')
                    ->label(__('filament.filters.filter_by_role'))
                    ->relationship('role', 'name'),
            ])
            ->actions([
                Tables\Actions\Action::make('approve')
                    ->label(__('filament.actions.approve'))
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (User $record) => !$record->is_approved)
                    ->action(fn (User $record) => $record->update(['is_approved' => true]))
                    ->requiresConfirmation(),
                Tables\Actions\Action::make('deactivate')
                    ->label(__('filament.actions.deactivate'))
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn (User $record) => $record->is_approved)
                    ->action(fn (User $record) => $record->update(['is_approved' => false]))
                    ->requiresConfirmation(),
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'email'];
    }
}
