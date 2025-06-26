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
        // Find the default document status that will be assigned to all new document requirements
        // This is typically 'Pendiente' status indicating documents are awaiting submission
        $defaultStatusId = DocumentStatus::where('is_default', true)->firstOrFail()->id;

        // Check if the newly created user has the 'provider' role
        // Only providers need document requirements, so we skip this process for regular users
        if ($user->hasRole('provider')) {
            // Get all active document types that are currently available in the system
            // Inactive document types are excluded to prevent assignment of deprecated requirements
            $activeDocumentTypeIds = DocumentType::where('is_active', true)->pluck('id')->toArray();

            // Create the pivot data array for bulk attachment
            // Each document type gets assigned with the default status and current timestamp
            $pivotData = [];
            foreach ($activeDocumentTypeIds as $documentTypeId) {
                $pivotData[$documentTypeId] = [
                    'document_status_id' => $defaultStatusId,
                    'created_at' => now(),
                    'updated_at' => now(),
                    // Note: file_path, uploaded_at, expires_at, and rejection_reason remain null
                    // These will be populated when the provider actually submits documents
                ];
            }

            // Bulk attach all active document types to the new provider
            // This creates entries in the provider_documents pivot table
            if (! empty($pivotData)) {
                $user->documentRequirements()->attach($pivotData);
            }
        }
    }

    /**
     * Handle the User "updated" event.
     */
    public function updated(User $user): void
    {
        //
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
