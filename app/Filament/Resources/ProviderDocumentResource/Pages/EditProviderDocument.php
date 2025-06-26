<?php

namespace App\Filament\Resources\ProviderDocumentResource\Pages;

use App\Filament\Resources\ProviderDocumentResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditProviderDocument extends EditRecord
{
    protected static string $resource = ProviderDocumentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title('Documento eliminado')
                        ->body('El documento ha sido eliminado del sistema.')
                        ->icon('heroicon-o-trash')
                        ->duration(5000)
                ),
        ];
    }

    /**
     * Override the redirect URL to go back to the index page after update
     */
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    /**
     * Override the saved notification to provide custom notification
     */
    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Documento actualizado')
            ->body('Los datos del documento han sido guardados.')
            ->icon('heroicon-o-document-check')
            ->duration(5000);
    }
}
