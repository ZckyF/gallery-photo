<?php

namespace App\Policies;

use App\Models\CommentPhoto;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class CommentPhotoPolicy
{
        /**
    * Determine whether the user can view any models.
    */
    public function getAll(User $user): bool
    {
        return $user->can('comment.all');
    }

    /**
    * Determine whether the user can view the model.
    */
    public function detail(User $user, CommentPhoto $commentPhoto): bool
    {
    return $user->id == $commentPhoto->user_id && $user->can('comment.detail');
    }

    /**
    * Determine whether the user can createuse models.
    */
    public function create(User $user): bool
    {
    return $user->can('comment.create');
    }

    /**
    * Determine whether the user can update the model.
    */
    public function update(User $user, CommentPhoto $commentPhoto): bool
    {
    return $user->id == $commentPhoto->user_id && $user->can('comment.update');
    }

    /**
    * Determine whether the user can delete the model.
    */
    public function delete(User $user, CommentPhoto $commentPhoto): bool
    {
    return $user->id == $commentPhoto->user_id && $user->can('comment.delete');
    }

    /**
    * Determine whether the user can restore the model.
    */
    public function restore(User $user, CommentPhoto $commentPhoto): bool
    {
    return false;
    }

    /**
    * Determine whether the user can permanently delete the model.
    */
    public function forceDelete(User $user, CommentPhoto $commentPhoto): bool
    {
    return false;
    }
}
