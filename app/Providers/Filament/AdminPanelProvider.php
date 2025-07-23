<?php

namespace App\Providers\Filament;

use App\Filament\Pages\Dashboard;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
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
use Illuminate\Support\Facades\Log;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->passwordReset()
            ->font('Poppins')
            ->brandLogo(fn () => view('filament.admin.logo'))
            ->colors([
                'primary' => [
                    '50' => '#f8f5f1',
                    '100' => '#ece6db',
                    '200' => '#d9cebf',
                    '300' => '#c5b6a3',
                    '400' => '#b29e87',
                    '500' => '#a28a70', // Un tono ligeramente más saturado que el base
                    '600' => '#857151', // Tu color base
                    '700' => '#6e5d48',
                    '800' => '#57493a',
                    '900' => '#40352b',
                    '950' => '#29221c',
                ],
                'danger' => Color::Red,
                'gray' => Color::Zinc,
                'info' => Color::Blue,
                'success' => Color::Green,
                'warning' => Color::Amber,
            ])
            // Role-based home URL redirection after login
            // Providers are sent to their dedicated dashboard, while admins go to the main admin panel
            ->homeUrl(function (): string {
                $user = auth()->user();

                if ($user) {
                    // Ensure the user model is fresh to get the latest roles
                    $user->load('roles'); // Eager load roles relationship
                    Log::info('User logged in: '.$user->email.' with roles: '.implode(', ', $user->getRoleNames()->toArray()));

                    if ($user->hasRole('Provider')) {
                        Log::info('Redirecting Provider user to /admin/documentacion');

                        return '/admin/documentacion';
                    }
                }

                Log::info('Redirecting non-Provider user to /admin');

                return '/admin';
            })
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                // Use our custom Dashboard that controls access by role
                \App\Filament\Pages\Dashboard::class,
                \App\Filament\Pages\ProviderDashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                // Removed default Filament widgets for cleaner admin dashboard
                // Widgets\AccountWidget::class,
                // Widgets\FilamentInfoWidget::class,
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
                \App\Http\Middleware\RedirectProviderToDashboard::class,
            ])
            ->plugins([
                FilamentShieldPlugin::make(),
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->resources([
                config('filament-logger.activity_resource'),
            ]);
    }
}
