<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Filament\Http\Responses\Auth\Contracts\LogoutResponse as LogoutResponseContract;
use App\Http\Responses\LogoutResponse;
use BezhanSalleh\FilamentLanguageSwitch\LanguageSwitch;
use Illuminate\Support\Facades\Blade;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(LogoutResponseContract::class, LogoutResponse::class);
    }

    public function boot(): void
    {
        \BezhanSalleh\FilamentLanguageSwitch\LanguageSwitch::configureUsing(function (\BezhanSalleh\FilamentLanguageSwitch\LanguageSwitch $switch) {
            $switch
                ->locales(['ar', 'en'])
                ->visible(outsidePanels: true)
                ->outsidePanelRoutes([
                    'filament.admin.auth.login',
                    'filament.admin.auth.password-reset',
                    'filament.pm.auth.login',
                    'filament.hr.auth.login',
                    'filament.employee.auth.login',
                    'filament.accountant.auth.login',
                    'filament.client.auth.login',
                ])
                ->labels([
                    'ar' => 'العربية',
                    'en' => 'English',
                ]);
        });

        Blade::directive('dir', function () {
            return "<?php echo app()->getLocale() === 'ar' ? 'rtl' : 'ltr'; ?>";
        });

        Blade::directive('langDir', function () {
            return "<?php echo app()->getLocale() === 'ar' ? 'dir=\"rtl\" lang=\"ar\"' : 'dir=\"ltr\" lang=\"en\"'; ?>";
        });

        Blade::directive('ar', function ($expression) {
            return "<?php echo e(\App\Support\Arabic::shape((string) ($expression))); ?>";
        });

    }
}