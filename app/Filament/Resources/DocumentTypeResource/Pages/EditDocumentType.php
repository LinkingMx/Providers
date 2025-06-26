<?php

namespace App\Filament\Resources\DocumentTypeResource\Pages;

use App\Filament\Resources\DocumentTypeResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditDocumentType extends EditRecord
{
    protected static string $resource = DocumentTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title('Tipo de documento eliminado')
                        ->body('El tipo de documento ha sido eliminado exitosamente.')
                        ->icon('heroicon-o-trash')
                        ->duration(5000)
                ),
        ];
    }

    /**
     * Override the getSavedNotification method to provide custom notification
     * for when a document type is successfully updated
     */
    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Tipo de documento actualizado')
            ->body("El tipo de documento '{$this->record->name}' ha sido actualizado exitosamente.")
            ->icon('heroicon-o-document-text')
            ->duration(5000); // Show for 5 seconds
    }

    /**
     * Custom redirect after editing - go back to the index page
     */
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
