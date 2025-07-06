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

    public function view(User $user): bool
    {
        return true;
    }

    public function viewAny(User $user): bool
    {
        return true;
    }

    public function viewAllComments(User $user): bool
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

    public function approve(User $user): bool
    {
        return $this->authCheck->isAdmin($user);
    }

    public function reject(User $user): bool
    {
        return $this->authCheck->isAdmin($user);
    }

    public function filter(User $user): bool
    {
        return $this->authCheck->isAdmin($user);
    }
}
