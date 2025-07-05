<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

trait HandlesOwnership
{
    private function isOwnerOrAdmin(User $user, Model $model): bool
    {
        return $user->id === $model->user_id || $user->isAdmin();
    }
}