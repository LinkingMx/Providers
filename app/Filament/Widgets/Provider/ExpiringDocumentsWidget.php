<?php

namespace App\Filament\Widgets\Provider;

use App\Models\ProviderDocument;
use Carbon\Carbon;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;

class ExpiringDocumentsWidget extends Widget
{
    protected static string $view = 'filament.widgets.provider.expiring-documents-widget';

    protected static ?int $sort = 2;

    protected static ?string $heading = 'Documentos por Vencer';

    public $expiringDocuments = [];

    public function mount()
    {
        $user = Auth::user();
        $this->expiringDocuments = ProviderDocument::where('user_id', $user->id)
            ->whereNotNull('expires_at')
            ->where('expires_at', '>=', now())
            ->where('expires_at', '<=', Carbon::now()->addDays(10))
            ->with(['documentType'])
            ->orderBy('expires_at')
            ->get();
    }

    public static function canView(): bool
    {
        return Auth::check() && Auth::user()->hasRole('Provider');
    }
}
