<?php

namespace App\Policies;

use App\Core\Contracts\Policies\AuthorizationCheckContract;
use App\Core\Enums\CommentStatus;
use App\Models\Comment;
use App\Models\User;

class CommentPolicy
{
    public function __construct(private AuthorizationCheckContract $authCheck)
    {
    }

    public function view(): bool
    {
        return true;
    }

    public function create(): bool
    {
        return true;
    }

    public function update(User $user): bool
    {
        return $this->authCheck->isAdmin($user);
    }

    public function delete(User $user): bool
    {
        return $this->authCheck->isAdmin($user);
    }

    public function approve(User $user): bool
    {
        return $this->authCheck->isAdmin($user);
    }

    public function reject(User $user): bool
    {
        return $this->authCheck->isAdmin($user);
    }
}
