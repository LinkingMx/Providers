<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\Provider\ProviderDocumentManagerWidget;
use App\Filament\Widgets\Provider\ProviderStatsWidget;
use Filament\Pages\Page;

/**
 * Provider Dashboard Page
 *
 * This is a specialized dashboard page exclusively for users with the 'provider' role.
 * It provides providers with a dedicated interface to manage their document submissions,
 * track compliance status, and view renewal requirements.
 *
 * The page is secured with role-based access control to ensure only authenticated
 * providers can access their dashboard, maintaining system security and data privacy.
 */
class ProviderDashboard extends Page
{
    /**
     * The custom route path for this page.
     *
     * Uses a user-friendly Spanish path that's easy to remember and type.
     * This appears in the browser URL when users navigate to their dashboard.
     */
    protected static string $route = '/mi-panel';

    /**
     * The navigation icon displayed in the sidebar menu.
     *
     * Uses a home icon to represent this as the provider's main dashboard,
     * making it intuitive for users to find their primary workspace.
     */
    protected static ?string $navigationIcon = 'heroicon-o-home';

    /**
     * The navigation label shown in the sidebar menu.
     *
     * Displays in Spanish to match the application's localization,
     * clearly identifying this as the user's personal panel.
     */
    protected static ?string $navigationLabel = 'Mi Panel';

    /**
     * Navigation sort order to ensure this appears first for providers.
     */
    protected static ?int $navigationSort = -10;

    /**
     * The page title displayed at the top of the dashboard.
     *
     * Provides a clear, descriptive title that explains the page's purpose
     * for document management and compliance tracking.
     */
    protected static ?string $title = 'Mi Panel de Documentos';

    /**
     * The Blade view file that renders this page's content.
     *
     * Points to the custom view template that will contain the provider-specific
     * dashboard widgets, document status cards, and compliance information.
     */
    protected static string $view = 'filament.pages.provider-dashboard';

    /**
     * Determine if the current user can access this page.
     *
     * This is the security gate that restricts access to only users with the 'provider' role.
     * It prevents administrators, regular users, and unauthenticated visitors from
     * accessing the provider dashboard, ensuring data privacy and system security.
     *
     * The method is called by Filament's authorization system before rendering the page
     * or including it in navigation menus.
     *
     * @return bool True if the current user has the 'provider' role, false otherwise
     */
    public static function canAccess(): bool
    {
        return auth()->user()?->hasRole('Provider') ?? false;
    }

    /**
     * Define widgets displayed in the header area of the dashboard.
     *
     * Header widgets appear at the top of the page and are typically used for
     * statistics, key metrics, or summary information. These widgets provide
     * providers with an immediate overview of their compliance status and
     * document requirements.
     *
     * The ProviderStatsWidget displays essential compliance metrics including
     * completion percentage, pending documents count, and expiration alerts.
     *
     * @return array Array of widget class names to be rendered in the header
     */
    protected function getHeaderWidgets(): array
    {
        return [
            ProviderStatsWidget::class,
        ];
    }

    /**
     * Define widgets displayed in the main content area of the dashboard.
     *
     * Main content widgets form the primary interface where users perform
     * their core tasks. These widgets are typically larger and more interactive,
     * providing full functionality for document management operations.
     *
     * The ProviderDocumentManagerWidget serves as the central hub for all
     * document-related activities including file uploads, status tracking,
     * and compliance management.
     *
     * @return array Array of widget class names to be rendered in the main content area
     */
    protected function getWidgets(): array
    {
        return [
            ProviderDocumentManagerWidget::class,
        ];
    }

    /**
     * Disable default Filament widgets by returning empty array.
     * This ensures only our custom provider widgets are shown.
     *
     * @return array Empty array to disable default widgets
     */
    public function getWidgetsColumns(): int|string|array
    {
        return 1; // Single column layout for cleaner appearance
    }

    /**
     * Configure header widget columns for better layout.
     *
     * @return int|string|array Number of columns for header widgets
     */
    public function getHeaderWidgetsColumns(): int|string|array
    {
        return 1; // Single column for header stats
    }
}
