<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;

/**
 * Custom Dashboard Page
 *
 * This replaces the default Filament Dashboard and controls access based on user roles.
 * Providers are redirected to their custom dashboard, while admins see the default dashboard.
 */
class Dashboard extends BaseDashboard
{
    /**
     * Control access to the default dashboard.
     * Only non-Provider users should see this page.
     */
    public static function canAccess(): bool
    {
        return auth()->check() && ! auth()->user()->hasRole('Provider');
    }

    /**
     * Additional redirect logic when mounting the dashboard
     */
    public function mount(): void
    {
        // Double-check and redirect Providers as a safety measure
        if (auth()->check() && auth()->user()->hasRole('Provider')) {
            \Illuminate\Support\Facades\Log::info('[Dashboard] Provider user attempted to access admin dashboard, redirecting to documentacion', [
                'user_id' => auth()->id(),
                'email' => auth()->user()->email
            ]);
            
            redirect('/admin/documentacion');
        }
    }

    /**
     * Override navigation label for clarity
     */
    public static function getNavigationLabel(): string
    {
        return 'Escritorio Administrativo';
    }

    /**
     * Override navigation sort to ensure proper ordering
     */
    public static function getNavigationSort(): ?int
    {
        return -2; // Show before other pages but after ProviderDashboard for providers
    }

    /**
     * Override page title
     */
    protected static ?string $title = 'Escritorio Administrativo';

    /**
     * Add admin-specific widgets for dashboard functionality
     */
    public function getWidgets(): array
    {
        return [
            \App\Filament\Widgets\Admin\StatsOverviewWidget::class,
            \App\Filament\Widgets\Admin\PendingDocumentsTableWidget::class,
        ];
    }

    /**
     * Remove header widgets as well
     */
    public function getHeaderWidgets(): array
    {
        return [
            // No header widgets for clean appearance
        ];
    }

    /**
     * Configure widget columns for better layout if widgets are added later
     */
    public function getWidgetsColumns(): int|string|array
    {
        return 2; // Two-column layout for future widgets
    }
}
