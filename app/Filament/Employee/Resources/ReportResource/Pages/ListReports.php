<?php

namespace App\Filament\Employee\Resources\ReportResource\Pages;

use App\Filament\Employee\Resources\ReportResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListReports extends ListRecords
{
    protected static string $resource = ReportResource::class;

    public function getTabs(): array
    {
        $employeeId = ReportResource::personalEmployeeId();

        return [
            'all' => Tab::make(__('filament.reports.tab_all')),
            'sent' => Tab::make(__('filament.reports.tab_sent'))
                ->modifyQueryUsing(fn (Builder $query) => $query->where('sender_id', $employeeId)),
            'received' => Tab::make(__('filament.reports.tab_received'))
                ->modifyQueryUsing(fn (Builder $query) => $query->where('receiver_id', $employeeId)),
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
