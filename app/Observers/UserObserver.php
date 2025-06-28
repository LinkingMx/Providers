<?php

namespace App\Observers;

use App\Jobs\AssignProviderDocumentsJob;
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
        // Logic moved to CreateUser page to ensure provider profile is created first.
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
        // Logic moved to EditUser page to ensure provider profile is updated first.
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