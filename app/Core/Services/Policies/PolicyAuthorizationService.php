<?php

namespace App\Core\Services\Policies;

use App\Core\Contracts\Policies\AuthorizationCheckContract;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

/**
 * @method static self isOwner(User $user, Model $model): bool
 * @method static self isAdmin(User $user): bool
 * @method static self canModerate(User $user): bool
 */
class PolicyAuthorizationService implements AuthorizationCheckContract
{
    public function isOwner(User $user, Model $model): bool
    {
        return $user->id === $model->user_id;
    }

    public function isAdmin(User $user): bool
    {
        return $user->isAdmin();
    }

    public function canModerate(User $user): bool
    {
        return $user->canModerate();
    }
}
