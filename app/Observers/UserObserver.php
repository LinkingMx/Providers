<?php

namespace App\Observers;

use App\Models\DocumentStatus;
use App\Models\DocumentType;
use App\Models\User;

class UserObserver
{
    /**
     * Handle the User "created" event.
     *
     * Automatically assigns all active document requirements to new providers
     * with the default status. This ensures that newly created provider accounts
     * have all necessary document requirements ready for submission.
     */
    public function created(User $user): void
    {
        // Check if the newly created user has the 'Provider' role (with capital P)
        // Only providers need document requirements, so we skip this process for regular users
        if ($user->hasRole('Provider')) {
            $this->assignProviderDocuments($user);
        }
    }

    /**
     * Handle the User "updated" event.
     *
     * This method is triggered when a user is updated, including when roles are assigned.
     * It checks if the user just received the 'Provider' role and ensures they have
     * the proper provider profile and document requirements.
     */
    public function updated(User $user): void
    {
        // Check if the user now has the 'Provider' role and doesn't have documents assigned
        if ($user->hasRole('Provider') && $user->documentRequirements()->count() === 0) {
            $this->assignProviderDocuments($user);
        }
    }

    /**
     * Assign document requirements to a provider user.
     *
     * This is a shared method used by both created() and updated() events
     * to ensure consistent document assignment logic.
     */
    private function assignProviderDocuments(User $user): void
    {
        // Find the default document status
        $defaultStatusId = DocumentStatus::where('is_default', true)->firstOrFail()->id;

        // Create provider profile if it doesn't exist
        if (! $user->providerProfile) {
            $user->providerProfile()->create([
                'business_name' => $user->name,
            ]);
        }

        // Get all active document types
        $activeDocumentTypeIds = DocumentType::where('is_active', true)->pluck('id')->toArray();

        // Create the pivot data array for bulk attachment
        $pivotData = [];
        foreach ($activeDocumentTypeIds as $documentTypeId) {
            $pivotData[$documentTypeId] = [
                'document_status_id' => $defaultStatusId,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        // Bulk attach all document types with default status
        $user->documentRequirements()->attach($pivotData);
    }

    /**
     * Handle the User "deleted" event.
     */
    public function deleted(User $user): void
    {
        //
    }

    /**
     * Handle the User "restored" event.
     */
    public function restored(User $user): void
    {
        //
    }

    /**
     * Handle the User "force deleted" event.
     */
    public function forceDeleted(User $user): void
    {
        //
    }
}
