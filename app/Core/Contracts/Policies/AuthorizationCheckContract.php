<?php

namespace App\Core\Contracts\Policies;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

interface AuthorizationCheckContract
{
    public function isOwner(User $user, Model $model): bool;
    public function isAdmin(User $user): bool;
    public function canModerate(User $user): bool;
}
