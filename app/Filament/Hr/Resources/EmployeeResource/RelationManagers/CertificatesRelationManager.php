<?php

namespace App\Filament\Hr\Resources\EmployeeResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class CertificatesRelationManager extends RelationManager
{
    protected static string $relationship = 'certificates';

    public static function getTitle(\Illuminate\Database\Eloquent\Model $ownerRecord, string $panelClass): string
    {
        return __('filament.relation.employee_certificates');
    }

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('title')
                ->label(__('filament.fields.certificate_title'))
                ->required(),
            Forms\Components\TextInput::make('issuer')
                ->label(__('filament.fields.issuer'))
                ->required(),
            Forms\Components\DatePicker::make('issue_date')
                ->label(__('filament.fields.issue_date'))
                ->required(),
            Forms\Components\DatePicker::make('expiry_date')
                ->label(__('filament.fields.expiry_date')),
            Forms\Components\FileUpload::make('certificate_url')
                ->label(__('filament.fields.certificate_file'))
                ->directory('certificates')
                ->columnSpanFull(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label(__('filament.fields.certificate_title'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('issuer')
                    ->label(__('filament.fields.issuer'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('issue_date')
                    ->label(__('filament.fields.issue_date'))
                    ->date(),
                Tables\Columns\TextColumn::make('expiry_date')
                    ->label(__('filament.fields.expiry_date'))
                    ->date(),
            ])
            ->headerActions([Tables\Actions\CreateAction::make()])
            ->actions([Tables\Actions\EditAction::make(), Tables\Actions\DeleteAction::make()]);
    }
}
