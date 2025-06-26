<?php

namespace App\Filament\Resources\DocumentStatusResource\Pages;

use App\Filament\Resources\DocumentStatusResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListDocumentStatuses extends ListRecords
{
    protected static string $resource = DocumentStatusResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Crear Estado')
                ->icon('heroicon-o-plus')
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title('Estado de documento creado')
                        ->body('El nuevo estado de documento ha sido creado exitosamente.')
                        ->icon('heroicon-o-check-circle')
                        ->duration(5000)
                ),
        ];
    }

    /**
     * Override method to handle custom notifications for bulk actions
     * if they are enabled in the future
     */
    protected function getDeletedNotification(): ?Notification
    {
        return Notification::make()
            ->warning()
            ->title('Eliminación no permitida')
            ->body('Los estados de documento no pueden ser eliminados para mantener la integridad de los datos.')
            ->icon('heroicon-o-exclamation-triangle')
            ->duration(6000);
    }
}
