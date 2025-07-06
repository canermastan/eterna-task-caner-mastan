<?php

namespace App\Policies;

use App\Core\Contracts\Policies\AuthorizationCheckContract;
use App\Models\Post;
use App\Models\User;

class PostPolicy
{
    public function __construct(private AuthorizationCheckContract $authCheck)
    {
    }

    public function view(): bool
    {
        return true;
    }

    public function viewAny(): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $this->authCheck->canModerate($user);
    }

    public function update(User $user, Post $post): bool
    {
        return $this->authCheck->isOwner($user, $post) || $this->authCheck->isAdmin($user);
    }

    public function delete(User $user): bool
    {
        return $this->authCheck->canModerate($user);
    }

    public function toggleDraftPublished(User $user): bool
    {
        return $this->authCheck->isAdmin($user);
    }

    public function viewAllPostsForAdmin(User $user): bool
    {
        return $this->authCheck->isAdmin($user);
    }

    public function viewMyPosts(User $user): bool
    {
        return $this->authCheck->canModerate($user);
    }
}
