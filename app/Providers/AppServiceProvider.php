<?php

namespace App\Providers;

use App\Core\Contracts\CategoryRepositoryInterface;
use App\Core\Contracts\CommentRepositoryInterface;
use App\Core\Contracts\Policies\AuthorizationCheckContract;
use App\Core\Contracts\PostRepositoryInterface;
use App\Core\Repositories\CategoryRepository;
use App\Core\Repositories\CommentRepository;
use App\Core\Repositories\PostRepository;
use App\Core\Services\Policies\PolicyAuthorizationService;
use App\Models\Comment;
use App\Models\Post;
use App\Policies\CommentPolicy;
use App\Policies\PostPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(PostRepositoryInterface::class, PostRepository::class);
        $this->app->bind(CommentRepositoryInterface::class, CommentRepository::class);
        $this->app->bind(CategoryRepositoryInterface::class, CategoryRepository::class);
        $this->app->bind(AuthorizationCheckContract::class, PolicyAuthorizationService::class);
    }

    public function boot(): void
    {
        Gate::policy(Post::class, PostPolicy::class);
        Gate::policy(Comment::class, CommentPolicy::class);
        Gate::policy(\App\Models\Category::class, \App\Policies\CategoryPolicy::class);
    }
}
