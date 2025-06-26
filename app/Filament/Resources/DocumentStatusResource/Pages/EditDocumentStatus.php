<?php

namespace App\Filament\Resources\DocumentStatusResource\Pages;

use App\Filament\Resources\DocumentStatusResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditDocumentStatus extends EditRecord
{
    protected static string $resource = DocumentStatusResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Remove delete action since we don't want to allow deletion
            // Actions\DeleteAction::make(),
        ];
    }

    /**
     * Override the getSavedNotification method to provide custom notification
     * for when a document status is successfully updated
     */
    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Estado de documento actualizado')
            ->body("El estado '{$this->record->name}' ha sido actualizado exitosamente.")
            ->icon('heroicon-o-pencil-square')
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
