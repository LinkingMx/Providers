<?php

namespace App\Policies;

use App\Models\User;

use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\User  $user
     * @return bool
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_user');
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $currentUser
     * @param  \App\Models\User  $targetUser
     * @return bool
     */
    public function view(User $currentUser, User $targetUser): bool
    {
        // Super admins can view any user
        if ($currentUser->hasRole('super_admin')) {
            return $currentUser->can('view_user');
        }

        // Admin users can only view Provider users
        if ($currentUser->hasRole('Admin')) {
            return $currentUser->can('view_user') && $targetUser->hasRole('Provider');
        }

        // Other users follow default permissions
        return $currentUser->can('view_user');
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return bool
     */
    public function create(User $user): bool
    {
        return $user->can('create_user');
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $currentUser
     * @param  \App\Models\User  $targetUser
     * @return bool
     */
    public function update(User $currentUser, User $targetUser): bool
    {
        // Super admins can update any user
        if ($currentUser->hasRole('super_admin')) {
            return $currentUser->can('update_user');
        }

        // Admin users can only update Provider users
        if ($currentUser->hasRole('Admin')) {
            return $currentUser->can('update_user') && $targetUser->hasRole('Provider');
        }

        // Other users follow default permissions
        return $currentUser->can('update_user');
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $currentUser
     * @param  \App\Models\User  $targetUser
     * @return bool
     */
    public function delete(User $currentUser, User $targetUser): bool
    {
        // Super admins can delete any user
        if ($currentUser->hasRole('super_admin')) {
            return $currentUser->can('delete_user');
        }

        // Admin users can only delete Provider users
        if ($currentUser->hasRole('Admin')) {
            return $currentUser->can('delete_user') && $targetUser->hasRole('Provider');
        }

        // Other users follow default permissions
        return $currentUser->can('delete_user');
    }

    /**
     * Determine whether the user can bulk delete.
     *
     * @param  \App\Models\User  $user
     * @return bool
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_user');
    }

    /**
     * Determine whether the user can permanently delete.
     *
     * @param  \App\Models\User  $user
     * @return bool
     */
    public function forceDelete(User $user): bool
    {
        return $user->can('force_delete_user');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     *
     * @param  \App\Models\User  $user
     * @return bool
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_user');
    }

    /**
     * Determine whether the user can restore.
     *
     * @param  \App\Models\User  $user
     * @return bool
     */
    public function restore(User $user): bool
    {
        return $user->can('restore_user');
    }

    /**
     * Determine whether the user can bulk restore.
     *
     * @param  \App\Models\User  $user
     * @return bool
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_user');
    }

    /**
     * Determine whether the user can bulk restore.
     *
     * @param  \App\Models\User  $user
     * @return bool
     */
    public function replicate(User $user): bool
    {
        return $user->can('replicate_user');
    }

    /**
     * Determine whether the user can reorder.
     *
     * @param  \App\Models\User  $user
     * @return bool
     */
    public function reorder(User $user): bool
    {
        return $user->can('reorder_user');
    }

    /**
     * Determine which roles the user can assign to other users.
     *
     * @param  \App\Models\User  $user
     * @param  array  $requestedRoles
     * @return bool
     */
    public function assignRoles(User $user, array $requestedRoles = []): bool
    {
        // Super admins can assign any role
        if ($user->hasRole('super_admin')) {
            return true;
        }

        // Admin users can only assign Provider roles
        if ($user->hasRole('Admin')) {
            // Get all role names from the requested roles array
            $roleNames = collect($requestedRoles)->map(function ($roleId) {
                return \Spatie\Permission\Models\Role::find($roleId)?->name;
            })->filter()->toArray();

            // Admin users can only assign Provider roles
            foreach ($roleNames as $roleName) {
                if (!in_array($roleName, ['Provider'])) {
                    return false;
                }
            }
            return true;
        }

        // Other users cannot assign any roles
        return false;
    }
}
