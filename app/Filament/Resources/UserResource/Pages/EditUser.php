<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title('Usuario eliminado')
                        ->body('El usuario ha sido eliminado del sistema.')
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
     * Handle the update of user records with provider profile support.
     *
     * This method updates both the User record and optionally creates/updates
     * a ProviderProfile record if the user has provider-specific data (RFC).
     *
     * @param  Model  $record  The user record being updated
     * @param  array  $data  The form data
     * @return Model The updated user model
     */
    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        // Extract provider profile data from the nested structure
        $providerData = $data['providerProfile'] ?? [];

        // Remove provider profile data from main user data to avoid conflicts
        unset($data['providerProfile']);

        // Update the user record with main user data (name, email, password if provided)
        $record->update($data);

        // If RFC exists and is not null, create or update the provider profile
        if (! empty($providerData['rfc'])) {
            // Use updateOrCreate to handle both creation and update scenarios
            $record->providerProfile()->updateOrCreate(
                ['user_id' => $record->id], // Search criteria
                [
                    'rfc' => $providerData['rfc'],
                    'business_name' => $providerData['business_name'] ?? null,
                ]
            );
        } else {
            // If RFC is removed/empty, we might want to delete the provider profile
            // This handles the case where a user is changed from provider to regular user
            $record->providerProfile()?->delete();
        }

        // Return the updated user record
        return $record;
    }

    /**
     * Override the saved notification to provide custom notification
     * for when a user is successfully updated
     */
    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Usuario actualizado')
            ->body('Los datos del usuario han sido guardados.')
            ->icon('heroicon-o-user')
            ->duration(5000); // Show for 5 seconds
    }

    /**
     * Prevent the default notification title to avoid duplicate notifications
     */
    protected function getSavedNotificationTitle(): ?string
    {
        return null;
    }

    /**
     * Hook called after the record has been updated and saved to the database.
     *
     * This method handles post-update logic including:
     * - Automatic assignment of document requirements when Provider role is assigned
     * - Automatic removal of document requirements when Provider role is removed
     */
    protected function afterSave(): void
    {
        if ($this->record->hasRole('Provider')) {
            // User HAS Provider role - assign documents if they don't have any
            if ($this->record->documentRequirements()->count() === 0) {
                \Artisan::call('provider:assign-documents', ['email' => $this->record->email]);
            }
        } else {
            // User DOES NOT have Provider role - remove all document requirements and profile
            if ($this->record->documentRequirements()->count() > 0) {
                // Use our clean command to remove documents and profile
                \Artisan::call('provider:clean-documents', ['email' => $this->record->email]);

                // Show notification about cleanup
                \Filament\Notifications\Notification::make()
                    ->warning()
                    ->title('Documentos de proveedor eliminados')
                    ->body('Se han eliminado los documentos requeridos porque el usuario ya no tiene rol de Provider.')
                    ->icon('heroicon-o-document-minus')
                    ->duration(5000)
                    ->send();
            }
        }
    }
}
