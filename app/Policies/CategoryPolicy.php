<?php

namespace App\Policies;

use App\Core\Contracts\Policies\AuthorizationCheckContract;
use App\Models\User;

class CategoryPolicy
{
    public function __construct(private AuthorizationCheckContract $authCheck)
    {
    }

    public function viewAny(): bool
    {
        return true;
    }

    public function view(User $user): bool
    {
        return $this->authCheck->isAdmin($user);
    }

    public function create(User $user): bool
    {
        return $this->authCheck->isAdmin($user);
    }

    public function update(User $user): bool
    {
        return $this->authCheck->isAdmin($user);
    }

    public function delete(User $user): bool
    {
        return $this->authCheck->isAdmin($user);
    }
}
