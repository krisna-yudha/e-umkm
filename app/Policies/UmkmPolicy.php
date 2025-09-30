<?php

namespace App\Policies;

use App\Models\Umkm;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class UmkmPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Users can view their own UMKM list
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Umkm $umkm): bool
    {
        // Users can only view their own UMKM
        return $user->id === $umkm->user_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // All authenticated users can create UMKM
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Umkm $umkm): bool
    {
        // Users can only update their own UMKM
        return $user->id === $umkm->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Umkm $umkm): bool
    {
        // Users can only delete their own UMKM
        return $user->id === $umkm->user_id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Umkm $umkm): bool
    {
        // Users can only restore their own UMKM
        return $user->id === $umkm->user_id;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Umkm $umkm): bool
    {
        // Users can only force delete their own UMKM
        return $user->id === $umkm->user_id;
    }
}
