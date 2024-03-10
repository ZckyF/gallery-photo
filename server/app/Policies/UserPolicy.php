<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /**
    * Determine whether the user can view any models.
    */
    public function getAll(User $user): bool
    {
        return $user->can('user.all');
    }

    /**
    * Determine whether the user can view the model.
    */
    public function detail(User $user): bool
    {
        return $user->can('user.detail');
    }

    /**
    * Determine whether the user can createuse models.
    */
    public function create(User $user): bool
    {
        return $user->can('user.create');
    }

    /**
    * Determine whether the user can update the model.
    */
    public function update(User $user): bool
    {
        return $user->can('user.update') || $user->can('user.detail');
    }

    /**
    * Determine whether the user can delete the model.
    */
    public function delete(User $user): bool
    {
        return $user->can('user.delete');
    }

    /**
    * Determine whether the user can restore the model.
    */
    public function restore(User $user): bool
    {
        return false;
    }

    /**
    * Determine whether the user can permanently delete the model.
    */
    public function forceDelete(User $user): bool
    {
        return false;
    }
}
