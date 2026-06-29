<?php

namespace App\Filament\Pm\Pages;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;

class Dashboard extends \Filament\Pages\Dashboard
{
    use HasFiltersForm;

    public function filtersForm(Form $form): Form
    {
        return $form
            ->schema([
                Section::make(__('filament.widgets.filters') ?? 'فلترة الإحصائيات')
                    ->schema([
                        DatePicker::make('startDate')
                            ->label(__('filament.widgets.start_date') ?? 'من تاريخ')
                            ->native(false)
                            ->displayFormat('Y-m-d'),
                        DatePicker::make('endDate')
                            ->label(__('filament.widgets.end_date') ?? 'إلى تاريخ')
                            ->native(false)
                            ->displayFormat('Y-m-d'),
                    ])
                    ->columns(2)
                    ->icon('heroicon-m-funnel'),
            ]);
    }
}
