<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class ProviderDashboard extends Page
{
    protected static string $route = '/provider-dashboard';

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationLabel = 'Documentación';

    protected static ?string $title = 'Documentación de proveedor';

    protected static ?int $navigationSort = -10;

    protected static string $view = 'filament.pages.provider-dashboard';

    public static function canAccess(): bool
    {
        return auth()->check() && auth()->user()->hasRole('Provider');
    }

    public function getColumns(): int|string|array
    {
        return 1;
    }
}
