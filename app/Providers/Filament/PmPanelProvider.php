<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use App\Filament\Pm\Pages\MyAttendance;
use App\Filament\Pm\Pages\Profile;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\View\PanelsRenderHook;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class PmPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('pm')
            ->path('pm')
            ->brandName(fn () => __('filament.brand.pm'))
            ->colors([
                'primary' => Color::Blue,
            ])
            ->darkMode(true)
            ->font('Cairo')
            ->sidebarCollapsibleOnDesktop()
            ->spa()
            ->databaseNotifications()
            ->databaseNotificationsPolling('30s')
            ->globalSearchKeyBindings(['command+k', 'ctrl+k'])
            ->maxContentWidth('full')
            ->renderHook(
                PanelsRenderHook::HEAD_END,
                fn (): string => '<link rel="stylesheet" href="' . asset('vendor/frappe-gantt/frappe-gantt.min.css') . '" data-navigate-track>'
                    . '<script src="' . asset('vendor/frappe-gantt/frappe-gantt.min.js') . '" data-navigate-track></script>'
            )
            ->discoverResources(in: app_path('Filament/Pm/Resources'), for: 'App\\Filament\\Pm\\Resources')
            ->discoverPages(in: app_path('Filament/Pm/Pages'), for: 'App\\Filament\\Pm\\Pages')
            ->pages([
                \App\Filament\Pm\Pages\Dashboard::class,
                Profile::class,
                MyAttendance::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Pm/Widgets'), for: 'App\\Filament\\Pm\\Widgets')
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
