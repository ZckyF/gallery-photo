<?php

namespace App\Policies;

use App\Models\Save;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class SavePolicy
{
    /**
    * Determine whether the user can view any models.
    */
    public function getAll(User $user): bool
    {
        return $user->can('save.all');
    }

    /**
    * Determine whether the user can view the model.
    */
    public function detail(User $user, Save $save): bool
    {
        return $user->id == $save->user_id && $user->can('save.detail');
    }

    /**
    * Determine whether the user can createuse models.
    */
    public function create(User $user): bool
    {
        return $user->can('save.create');
    }

    /**
    * Determine whether the user can update the model.
    */
    public function update(User $user, Save $save): bool
    {
        return $user->id == $save->user_id && $user->can('save.update');
    }

    /**
    * Determine whether the user can delete the model.
    */
    public function delete(User $user, Save $save): bool
    {
        return $user->id == $save->user_id && $user->can('save.delete');
    }

    /**
    * Determine whether the user can restore the model.
    */
    public function restore(User $user, Save $save): bool
    {
        return false;
    }

    /**
    * Determine whether the user can permanently delete the model.
    */
    public function forceDelete(User $user, Save $save): bool
    {
        return false;
    }
}
