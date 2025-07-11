<?php

namespace App\Policies;

use App\Core\Contracts\Policies\AuthorizationCheckContract;
use App\Core\Enums\PostStatus;
use App\Models\Post;
use App\Models\User;

class PostPolicy
{
    public function __construct(private AuthorizationCheckContract $authCheck)
    {
    }

    public function view(?User $user, Post $post): bool
    {
        if ($post->status == PostStatus::DRAFT) {
            return $this->authCheck->isOwner($user, $post) || $this->authCheck->isAdmin($user);
        }
        return true;
    }

    public function viewAny(User $user): bool
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

    public function delete(User $user, Post $post): bool
    {
        return $this->authCheck->isOwner($user, $post) || $this->authCheck->isAdmin($user);
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
