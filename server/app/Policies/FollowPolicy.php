<?php

namespace App\Policies;

use App\Models\Follow;
use App\Models\User;

class FollowPolicy
{
        /**
    * Determine whether the user can view any models.
    */
    public function getAll(User $user): bool
    {
    return $user->can('follow.all');
    }

    /**
    * Determine whether the user can view the model.
    */
    public function detail(User $user, Follow $follow): bool
    {
    return $user->can('follow.detail');
    }

    /**
    * Determine whether the user can createuse models.
    */
    public function create(User $user): bool
    {
    return $user->can('follow.create');
    }

    /**
    * Determine whether the user can update the model.
    */
    public function update(User $user, Follow $follow): bool
    {
    return $user->id == $follow->user_id && $user->can('follow.update');
    }

    /**
    * Determine whether the user can delete the model.
    */
    public function delete(User $user, Follow $follow): bool
    {
    return  $user->can('follow.delete');
    }

    /**
    * Determine whether the user can restore the model.
    */
    public function restore(User $user, Follow $follow): bool
    {
    return false;
    }

    /**
    * Determine whether the user can permanently delete the model.
    */
    public function forceDelete(User $user, Follow $follow): bool
    {
        return false;
    }
}
