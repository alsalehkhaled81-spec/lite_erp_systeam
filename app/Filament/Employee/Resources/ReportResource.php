<?php

namespace App\Filament\Employee\Resources;

use App\Filament\Concerns\HandlesPersonalReports;
use App\Filament\Employee\Resources\ReportResource\Pages;
use App\Models\Report;
use Filament\Resources\Resource;

class ReportResource extends Resource
{
    use HandlesPersonalReports;

    protected static ?string $model = Report::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-chart-bar';
    protected static ?string $navigationGroup = null;

    public static function getNavigationGroup(): ?string
    {
        return __('filament.group.reports');
    }

    public static function getNavigationLabel(): string
    {
        return __('filament.nav.reports');
    }

    public static function getModelLabel(): string
    {
        return __('filament.model.report');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament.model.reports');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListReports::route('/'),
            'create' => Pages\CreateReport::route('/create'),
            'view' => Pages\ViewReport::route('/{record}'),
            'edit' => Pages\EditReport::route('/{record}/edit'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['title'];
    }
}
