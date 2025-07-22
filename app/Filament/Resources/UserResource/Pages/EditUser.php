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
        $rfc = $data['rfc'] ?? null;
        $businessName = $data['business_name'] ?? null;
        $providerTypeId = $data['provider_type_id'] ?? null;

        unset($data['rfc'], $data['business_name'], $data['provider_type_id']);

        $record->update($data);

        if (! empty($rfc)) {
            $record->providerProfile()->updateOrCreate(
                ['user_id' => $record->id],
                [
                    'rfc' => $rfc,
                    'business_name' => $businessName,
                    'provider_type_id' => $providerTypeId,
                ]
            );
        } else {
            $record->providerProfile()?->delete();
        }

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
     * - Sending welcome email when Provider role is assigned for the first time
     */
    protected function afterSave(): void
    {
        $hasProviderRole = $this->record->hasRole('Provider');
        $hadProviderRole = $this->record->getOriginal() ? 
            $this->record->newQuery()->find($this->record->id)->hasRole('Provider') : false;

        if ($hasProviderRole) {
            // User HAS Provider role - assign documents. The job will not re-assign if they already exist.
            \App\Jobs\AssignProviderDocumentsJob::dispatch($this->record->fresh());

            // If this is the first time the user gets the Provider role, send welcome email
            if (!$hadProviderRole) {
                \Illuminate\Support\Facades\Log::info('[EditUser] Provider role assigned for first time. Dispatching welcome email.', [
                    'user_id' => $this->record->id, 
                    'email' => $this->record->email
                ]);
                \App\Jobs\SendProviderWelcomeEmail::dispatch($this->record->fresh());
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

    /**
     * Mutate form data before filling the form.
     *
     * This method ensures that provider profile data is loaded into the form
     * when editing a user with a provider profile.
     */
    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Poblar los campos planos si existe providerProfile
        if ($this->record->providerProfile) {
            $data['rfc'] = $this->record->providerProfile->rfc;
            $data['business_name'] = $this->record->providerProfile->business_name;
            $data['provider_type_id'] = $this->record->providerProfile->provider_type_id;
        }

        return $data;
    }
}
