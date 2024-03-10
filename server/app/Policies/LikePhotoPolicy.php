<?php

namespace App\Policies;

use App\Models\LikePhoto;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class LikePhotoPolicy
{
    /**
    * Determine whether the user can view any models.
    */
    public function getAll(User $user): bool
    {
        return $user->can('like.all');
    }

    /**
    * Determine whether the user can view the model.
    */
    public function detail(User $user, LikePhoto $likePhoto): bool
    {
        return $user->id == $likePhoto->user_id && $user->can('like.detail');
    }

    /**
    * Determine whether the user can createuse models.
    */
    public function create(User $user): bool
    {
        return $user->can('like.create');
    }

    /**
    * Determine whether the user can update the model.
    */
    public function update(User $user, LikePhoto $likePhoto): bool
    {
        return $user->id == $likePhoto->user_id && $user->can('like.update');
    }

    /**
    * Determine whether the user can delete the model.
    */
    public function delete(User $user, LikePhoto $likePhoto): bool
    {
        return $user->id == $likePhoto->user_id && $user->can('like.delete');
    }

    /**
    * Determine whether the user can restore the model.
    */
    public function restore(User $user, LikePhoto $likePhoto): bool
    {
        return false;
    }

    /**
    * Determine whether the user can permanently delete the model.
    */
    public function forceDelete(User $user, LikePhoto $likePhoto): bool
    {
        return false;
    }
}
