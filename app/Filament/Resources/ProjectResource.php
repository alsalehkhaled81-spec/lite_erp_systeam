<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Pm\Resources\ProjectResource\RelationManagers\EmployeesRelationManager;
use App\Filament\Pm\Resources\ProjectResource\RelationManagers\TasksRelationManager;
use App\Filament\Resources\ProjectResource\Pages;
use App\Filament\Resources\ProjectResource\RelationManagers;
use App\Models\Project;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProjectResource extends Resource
{
    protected static ?string $model = Project::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('تفاصيل المشروع')
                    ->schema([
                        Forms\Components\Select::make('client_id')
                            ->label('العميل')
                            ->relationship('client', 'name')
                            ->searchable(),
                        Forms\Components\TextInput::make('name')
                            ->label('اسم المشروع')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Textarea::make('description')
                            ->label('وصف المشروع')
                            ->columnSpanFull(),
                    ])->columns(2),

                Forms\Components\Section::make('المالية والزمنية')
                    ->schema([
                        Forms\Components\TextInput::make('budget')
                            ->label('الميزانية')
                            ->numeric()
                            ->prefix('$'),
                        Forms\Components\Select::make('status')
                            ->label('حالة المشروع')
                            ->options([
                                'pending' => 'قيد الانتظار',
                                'in_progress' => 'قيد التنفيذ',
                                'completed' => 'مكتمل',
                                'canceled' => 'ملغى',
                            ])->default('pending'),
                        Forms\Components\DatePicker::make('start_date')
                            ->label('تاريخ البدء'),
                        Forms\Components\DatePicker::make('end_date')
                            ->label('تاريخ الانتهاء'),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('اسم المشروع')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('client.name')
                    ->label('العميل')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('budget')
                    ->label('الميزانية')
                    ->money('usd')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('الحالة')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'pending' => 'gray',
                        'in_progress' => 'warning',
                        'completed' => 'success',
                        'canceled' => 'danger',
                    }),

                Tables\Columns\TextColumn::make('start_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('end_date')
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
                ->label('حالة المشروع')
                ->options([
                    'pending' => 'قيد الانتظار',
                    'in_progress' => 'قيد التنفيذ',
                    'completed' => 'مكتمل',
                    'canceled' => 'ملغى',
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
                    EmployeesRelationManager::class,
                    TasksRelationManager::class,


        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProjects::route('/'),
            'create' => Pages\CreateProject::route('/create'),
            'edit' => Pages\EditProject::route('/{record}/edit'),
        ];
    }
}
