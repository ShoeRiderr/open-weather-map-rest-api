<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;

class UserPolicy
{
    /**
     * Determine whether the user can permanently delete the model.
     */
    public function delete(User $user, User $model): bool
    {
        return $user->id == $model->id && $user->id == auth()->id();
    }
}
