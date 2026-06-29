<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use App\Filament\Accountant\Pages\MyAttendance;
use App\Filament\Accountant\Pages\Profile;
use App\Filament\Accountant\Widgets\CashflowChart;
use App\Filament\Accountant\Widgets\OverdueInvoicesAlert;
use App\Filament\Accountant\Widgets\ProjectBudgetChart;
use App\Filament\Accountant\Widgets\TaxReportWidget;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AccountantPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('accountant')
            ->path('accountant')
            ->brandName(fn () => __('filament.brand.accountant'))
            ->colors([
                'primary' => Color::Emerald,
            ])
            ->darkMode(true)
            ->font('Cairo')
            ->sidebarCollapsibleOnDesktop()
            ->spa()
            ->databaseNotifications()
            ->databaseNotificationsPolling('30s')
            ->globalSearchKeyBindings(['command+k', 'ctrl+k'])
            ->maxContentWidth('full')
            ->discoverResources(in: app_path('Filament/Accountant/Resources'), for: 'App\\Filament\\Accountant\\Resources')
            ->discoverPages(in: app_path('Filament/Accountant/Pages'), for: 'App\\Filament\\Accountant\\Pages')
            ->pages([
                Pages\Dashboard::class,
                Profile::class,
                MyAttendance::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Accountant/Widgets'), for: 'App\\Filament\\Accountant\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
