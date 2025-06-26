<?php

namespace App\Filament\Resources\DocumentStatusResource\Pages;

use App\Filament\Resources\DocumentStatusResource;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateDocumentStatus extends CreateRecord
{
    protected static string $resource = DocumentStatusResource::class;

    /**
     * Override the getCreatedNotification method to provide custom notification
     * for when a document status is successfully created
     */
    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Estado de documento creado')
            ->body('El estado de documento ha sido creado exitosamente.')
            ->icon('heroicon-o-check-circle')
            ->duration(5000); // Show for 5 seconds
    }

    /**
     * Custom redirect after creation - stay on the create page for easier bulk creation
     */
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
