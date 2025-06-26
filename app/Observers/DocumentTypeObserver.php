<?php

namespace App\Observers;

use App\Models\DocumentStatus;
use App\Models\DocumentType;
use App\Models\User;

class DocumentTypeObserver
{
    /**
     * Handle the DocumentType "created" event.
     *
     * Automatically assigns newly created document types to all existing providers
     * with the default status. This ensures that when new document requirements
     * are added to the system, all providers immediately have these requirements
     * available for submission without manual intervention.
     */
    public function created(DocumentType $documentType): void
    {
        // Only process if the document type is active
        // Inactive document types should not be assigned to providers
        if (! $documentType->is_active) {
            return;
        }

        // Find the default document status that will be assigned to all new document requirements
        // This is typically 'Pendiente' status indicating documents are awaiting submission
        $defaultStatusId = DocumentStatus::where('is_default', true)->firstOrFail()->id;

        // Get all users who have the 'provider' role
        // These are the users who need document requirements and compliance tracking
        $providers = User::role('provider')->get();

        // Iterate through each provider and assign the new document type requirement
        foreach ($providers as $provider) {
            // Check if the provider doesn't already have this document type
            // This prevents duplicate entries if the observer runs multiple times
            if (! $provider->documentRequirements()->where('document_type_id', $documentType->id)->exists()) {
                // Attach the new document type to the provider with default status
                // This creates an entry in the provider_documents pivot table
                $provider->documentRequirements()->attach($documentType->id, [
                    'document_status_id' => $defaultStatusId,
                    'created_at' => now(),
                    'updated_at' => now(),
                    // Note: file_path, uploaded_at, expires_at, and rejection_reason remain null
                    // These will be populated when the provider actually submits the document
                ]);
            }
        }
    }

    /**
     * Handle the DocumentType "updated" event.
     */
    public function updated(DocumentType $documentType): void
    {
        //
    }

    /**
     * Handle the DocumentType "deleted" event.
     */
    public function deleted(DocumentType $documentType): void
    {
        //
    }

    /**
     * Handle the DocumentType "restored" event.
     */
    public function restored(DocumentType $documentType): void
    {
        //
    }

    /**
     * Handle the DocumentType "force deleted" event.
     */
    public function forceDeleted(DocumentType $documentType): void
    {
        //
    }
}
