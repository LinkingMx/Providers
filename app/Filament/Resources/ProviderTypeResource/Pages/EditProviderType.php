<?php

namespace App\Filament\Resources\ProviderTypeResource\Pages;

use App\Filament\Resources\ProviderTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditProviderType extends EditRecord
{
    protected static string $resource = ProviderTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    /**
     * Customize the Filament notification for successful update.
     */
    protected function getSavedNotification(): \Filament\Notifications\Notification
    {
        return \Filament\Notifications\Notification::make()
            ->title('¡Tipo de proveedor actualizado!')
            ->body('El tipo de proveedor fue actualizado correctamente.')
            ->icon('heroicon-o-check-circle')
            ->iconColor('success');
    }

    /**
     * Customize the Filament notification for successful deletion.
     */
    protected function getDeletedNotification(): \Filament\Notifications\Notification
    {
        return \Filament\Notifications\Notification::make()
            ->title('¡Tipo de proveedor eliminado!')
            ->body('El tipo de proveedor fue eliminado correctamente.')
            ->icon('heroicon-o-trash')
            ->iconColor('danger');
    }
}
