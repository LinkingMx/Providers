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
        $rfc = $data['rfc'] ?? null;
        $businessName = $data['business_name'] ?? null;
        $providerTypeId = $data['provider_type_id'] ?? null;
        $roles = $data['roles'] ?? [];

        // Validate role assignment permissions using policy
        $currentUser = auth()->user();
        if (!$currentUser->can('assignRoles', [User::class, $roles])) {
            throw new \Illuminate\Auth\Access\AuthorizationException('No tienes permisos para asignar estos roles.');
        }

        unset($data['rfc'], $data['business_name'], $data['provider_type_id']);

        $user = User::create($data);

        // Assign roles and refresh the user model to ensure roles are loaded.
        if (! empty($roles)) {
            $user->roles()->sync($roles);
            $user = $user->fresh();
        }

        // Store provider-specific data in a temporary property to be used in afterCreate
        $user->temp_provider_data = [
            'rfc' => $rfc,
            'business_name' => $businessName,
            'provider_type_id' => $providerTypeId,
        ];

        return $user;
    }

    protected function afterCreate(): void
    {
        $user = $this->record; // The created user record

        // Retrieve provider-specific data from the temporary property
        $rfc = $user->temp_provider_data['rfc'] ?? null;
        $businessName = $user->temp_provider_data['business_name'] ?? null;
        $providerTypeId = $user->temp_provider_data['provider_type_id'] ?? null;

        // If the user is a Provider, create their profile and dispatch the document assignment job.
        // At this point, roles are fully persisted and loaded.
        if ($user->hasRole('Provider')) {
            ProviderProfile::create([
                'user_id' => $user->id,
                'rfc' => $rfc,
                'business_name' => $businessName,
                'provider_type_id' => $providerTypeId,
            ]);

            // Dispatch the job to assign documents.
            // The job itself will handle checking for the provider_type_id and active documents.
            \Illuminate\Support\Facades\Log::debug('[CreateUser] User has provider role. Dispatching AssignProviderDocumentsJob.', ['user_id' => $user->id, 'provider_type_id' => $providerTypeId]);
            \App\Jobs\AssignProviderDocumentsJob::dispatch($user->fresh());

            // Dispatch the welcome email job
            \Illuminate\Support\Facades\Log::info('[CreateUser] Dispatching welcome email for new provider.', ['user_id' => $user->id, 'email' => $user->email]);
            \App\Jobs\SendProviderWelcomeEmail::dispatch($user->fresh());
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
