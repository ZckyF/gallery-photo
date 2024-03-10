<?php

namespace App\Policies;

use App\Models\Folder;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class FolderPolicy
{
            /**
     * Determine whether the user can view any models.
     */
    public function getAll(User $user): bool
    {
        return $user->can('folder.all');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function detail(User $user, Folder $folder): bool
    {
        return $user->id == $folder->user_id && $user->can('folder.detail');
    }

    /**
     * Determine whether the user can createuse models.
     */
    public function create(User $user): bool
    {
        return $user->can('folder.create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Folder $folder): bool
    {
        return $user->id == $folder->user_id && $user->can('folder.update');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Folder $folder): bool
    {
        return $user->id == $folder->user_id && $user->can('folder.delete');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Folder $folder): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Folder $folder): bool
    {
        return false;
    }
}
