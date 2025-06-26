<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Models\ProviderProfile;
use App\Models\User;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    /**
     * Override the redirect URL to go back to the index page after creation
     */
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    /**
     * Handle the creation of user records with provider profile support.
     *
     * This method creates both the User record and optionally a ProviderProfile
     * record if the user has provider-specific data (RFC).
     *
     * @param  array  $data  The form data
     * @return Model The created user model
     */
    protected function handleRecordCreation(array $data): Model
    {
        // Extract provider profile data from the nested structure
        $providerData = $data['providerProfile'] ?? [];

        // Remove provider profile data from main user data to avoid conflicts
        unset($data['providerProfile']);

        // Create the user record with main user data (name, email, password)
        $user = User::create($data);

        // If RFC exists and is not null, create a provider profile
        if (! empty($providerData['rfc'])) {
            // Create the provider profile record
            ProviderProfile::create([
                'user_id' => $user->id,
                'rfc' => $providerData['rfc'],
                'business_name' => $providerData['business_name'] ?? null,
            ]);
        }

        // Return the created user object
        return $user;
    }

    /**
     * Hook called after the record has been created and saved to the database.
     *
     * This method handles post-creation logic including automatic assignment
     * of document requirements for providers.
     */
    protected function afterCreate(): void
    {
        // Check if the created user has the Provider role
        if ($this->record->hasRole('Provider')) {
            // Automatically assign document requirements
            \Artisan::call('provider:assign-documents', ['email' => $this->record->email]);
        }
    }

    /**
     * Override the created notification to provide custom notification
     * for when a user is successfully created
     */
    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Usuario creado')
            ->body('El nuevo usuario ha sido registrado en el sistema.')
            ->icon('heroicon-o-user-plus')
            ->duration(5000); // Show for 5 seconds
    }

    /**
     * Prevent the default notification title to avoid duplicate notifications
     */
    protected function getCreatedNotificationTitle(): ?string
    {
        return null;
    }
}
