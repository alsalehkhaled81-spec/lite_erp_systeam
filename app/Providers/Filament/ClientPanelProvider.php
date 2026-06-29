<?php

namespace App\Providers\Filament;

use App\Filament\Client\Pages\ClientDashboard;
use App\Filament\Client\Pages\ClientProfile;
use App\Filament\Client\Widgets\ClientProjectsChart;
use App\Filament\Client\Widgets\ClientStatsOverview;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
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

class ClientPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('client')
            ->path('client')
            ->brandName(fn () => __('filament.brand.client'))
            ->authGuard('client')
            ->colors([
                'primary' => Color::Teal,
            ])
            ->darkMode(true)
            ->font('Cairo')
            ->sidebarCollapsibleOnDesktop()
            ->spa()
            ->globalSearchKeyBindings(['command+k', 'ctrl+k'])
            ->maxContentWidth('full')
            ->databaseNotifications()
            ->databaseNotificationsPolling('30s')
            ->discoverResources(in: app_path('Filament/Client/Resources'), for: 'App\\Filament\\Client\\Resources')
            ->discoverPages(in: app_path('Filament/Client/Pages'), for: 'App\\Filament\\Client\\Pages')
            ->pages([
                ClientDashboard::class,
                ClientProfile::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Client/Widgets'), for: 'App\\Filament\\Client\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
                ClientStatsOverview::class,
                ClientProjectsChart::class,
                \App\Filament\Client\Widgets\ClientTasksStatusChart::class,
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
