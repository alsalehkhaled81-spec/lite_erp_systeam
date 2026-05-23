<?php

namespace App\Filament\Hr\Resources;

use App\Filament\Hr\Resources\ReportResource\Pages;
use App\Models\Report;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ReportResource extends Resource
{
    protected static ?string $model = Report::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationLabel = 'التقارير الواردة';

    protected static ?string $modelLabel = 'تقرير';

    protected static ?string $pluralModelLabel = 'التقارير الواردة';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('التقرير')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label('عنوان التقرير')
                            ->disabled(),
                        Forms\Components\Textarea::make('content')
                            ->label('المحتوى')
                            ->disabled()
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('feedback')
                            ->label('الرد')
                            ->columnSpanFull(),
                        Forms\Components\Select::make('status')
                            ->label('الحالة')
                            ->options([
                                'unread' => 'غير مقروء',
                                'read' => 'مقروء',
                                'replied' => 'تم الرد',
                            ])
                            ->required(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('العنوان')
                    ->searchable()
                    ->limit(30),
                Tables\Columns\TextColumn::make('sender.user.name')
                    ->label('المرسل')
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('الحالة')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'unread' => 'warning',
                        'read' => 'info',
                        'replied' => 'success',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'unread' => 'غير مقروء',
                        'read' => 'مقروء',
                        'replied' => 'تم الرد',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاريخ الإرسال')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('الحالة')
                    ->options([
                        'unread' => 'غير مقروء',
                        'read' => 'مقروء',
                        'replied' => 'تم الرد',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('عرض/رد'),
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
            'index' => Pages\ListReports::route('/'),
            'edit' => Pages\EditReport::route('/{record}/edit'),
        ];
    }

    public static function getModelLabel(): string
    {
        return __('filament.model.report');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament.model.reports');
    }

    public static function getNavigationLabel(): string
    {
        return __('filament.nav.reports');
    }
}
