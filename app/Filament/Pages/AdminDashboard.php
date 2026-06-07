<?php

namespace App\Filament\Pages;

use App\Services\DashboardExportService;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Actions\Action;

class AdminDashboard extends BaseDashboard
{
    protected function getHeaderActions(): array
    {
        return [
            Action::make('export_pdf')
                ->label(__('filament.actions.export_dashboard_pdf'))
                ->icon('heroicon-o-document-arrow-down')
                ->color('success')
                ->action(fn () => app(DashboardExportService::class)->generatePdf()),
        ];
    }
}