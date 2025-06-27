<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class ProviderDashboard extends Page
{
    protected static string $route = '/mi-panel';

    protected static ?string $navigationIcon = 'heroicon-o-home';

    protected static ?string $navigationLabel = 'Mi Panel';

    protected static ?string $title = 'Mi Panel de Documentos';

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
