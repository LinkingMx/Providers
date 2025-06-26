<?php

namespace App\Filament\Resources\DocumentTypeResource\Pages;

use App\Filament\Resources\DocumentTypeResource;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateDocumentType extends CreateRecord
{
    protected static string $resource = DocumentTypeResource::class;

    /**
     * Override the getCreatedNotification method to provide custom notification
     * for when a document type is successfully created
     */
    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Tipo de documento creado')
            ->body('El tipo de documento ha sido creado exitosamente y asignado a todos los proveedores.')
            ->icon('heroicon-o-document-text')
            ->duration(5000); // Show for 5 seconds
    }

    /**
     * Custom redirect after creation - go back to the index page
     */
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
