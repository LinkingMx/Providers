<?php

namespace App\Filament\Resources\ProviderTypeResource\Pages;

use App\Filament\Resources\ProviderTypeResource;
use Filament\Resources\Pages\CreateRecord;

class CreateProviderType extends CreateRecord
{
    protected static string $resource = ProviderTypeResource::class;

    /**
     * Customize the Filament notification for successful creation.
     */
    protected function getCreatedNotification(): \Filament\Notifications\Notification
    {
        return \Filament\Notifications\Notification::make()
            ->title('¡Tipo de proveedor creado!')
            ->body('El tipo de proveedor fue registrado correctamente.')
            ->icon('heroicon-o-check-circle')
            ->iconColor('success');
    }
}
