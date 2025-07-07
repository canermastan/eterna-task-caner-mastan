<?php

namespace App\Core\Services;

use App\Core\Constants\Cache;
use App\Core\Contracts\PostRepositoryInterface;
use App\Core\Data\Dtos\Post\CreatePostDto;
use App\Core\Data\Dtos\Post\TogglePublishStatusDto;
use App\Core\Data\Dtos\Post\UpdatePostDto;
use App\Core\Enums\PostStatus;
use App\Events\PostPublished;
use App\Events\PostStatusUpdated;
use App\Models\Post;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache as CacheFacade;
use Illuminate\Support\Str;

class PostService
{
    public function __construct(
        private PostRepositoryInterface $postRepository
    ) {}

    public function getAll(): Collection
    {
        return CacheFacade::remember(Cache::getPostKey('all'), Cache::getTTL('long'), function () {
                return $this->postRepository->getAll();
            }
        );
    }

    public function getAllWithPagination(int $perPage = 15, int $page = 1): LengthAwarePaginator
    {
        $cacheKey = Cache::getPostKey("paginated_{$perPage}_page_{$page}");
        return CacheFacade::remember($cacheKey, Cache::getTTL('medium'), function () use ($perPage, $page) {
            return $this->postRepository->getAllWithPagination($perPage, $page);
        });
    }

    public function getById(int $id): Post
    {
        return CacheFacade::remember(Cache::getPostKey("id_{$id}"), Cache::getTTL('long'), function () use ($id) {
            return $this->postRepository->getByIdWithUserAndCategory($id);
        });
    }

    public function getBySlug(string $slug): Post
    {
        return CacheFacade::remember(Cache::getPostKey("slug_{$slug}"), Cache::getTTL('long'), function () use ($slug) {    
            return $this->postRepository->getBySlugWithUserAndCategory($slug);
        });
    }

    public function createPost(CreatePostDto $postDto, User $user): Post
    {
        $status = $this->determineStatus($user);

        $post = $this->postRepository->create([
            'user_id' => $user->id,
            'title' => $postDto->title,
            'content' => $postDto->content,
            'status' => $status,
            'published_at' => $status === PostStatus::PUBLISHED ? now() : null,
            'slug' => $this->generateUniqueSlug($postDto->title),
        ]);

        if (!empty($postDto->categoryIds)) {
            $post->categories()->attach($postDto->categoryIds);

            activity()
                ->performedOn($post)
                ->causedBy($user)
                ->log('Post categories added');
        }

        if ($postDto->coverImage && $postDto->coverImage->isValid()) {
            $post->addMedia($postDto->coverImage->getPathname())
                ->toMediaCollection('cover_images');

            activity()
                ->performedOn($post)
                ->causedBy($user)
                ->log('Post cover image added');
        }

        $post->load(['user', 'categories']);

        if ($status === PostStatus::PUBLISHED) {
            PostPublished::dispatch($post);
        }

        $this->clearCache($post);
        return $post;
    }

    public function toggleDraftPublished(int $postId): Post
    {
        $post = $this->postRepository->getByIdWithUserAndCategory($postId);

        $previousStatus = $post->status;
        $isFirstPublication = $post->published_at === null;
        $publishedAt = $isFirstPublication ? now() : $post->published_at;

        $newStatus = $post->status === PostStatus::DRAFT
            ? PostStatus::PUBLISHED
            : PostStatus::DRAFT;

        $dto = new TogglePublishStatusDto(
            id: $post->id,
            status: $newStatus,
            publishedAt: $publishedAt
        );

        $updatedPost = $this->postRepository->toggleDraftPublished($dto);

        if ($newStatus === PostStatus::PUBLISHED) {
            $isFirstPublication
                ? PostPublished::dispatch($updatedPost)
                : PostStatusUpdated::dispatch($updatedPost, $previousStatus);
        } elseif ($newStatus === PostStatus::DRAFT) {
            PostStatusUpdated::dispatch($updatedPost, $previousStatus);
        }

        $this->clearCache($updatedPost);
        return $updatedPost;
    }

    public function update(int $id, UpdatePostDto $updatePostDto, User $user): Post
    {
        // if user is a writer, we need to set the status to draft again and set the published_at to null
        $updatePostDto->status = $this->determineStatus($user);
        $updatePostDto->published_at = $updatePostDto->status === PostStatus::DRAFT ? null : now();

        $postDb = $this->postRepository->getById($id);

        $oldCategoryIds = $postDb->categories->pluck('id')->toArray();
        $newCategoryIds = $updatePostDto->categoryIds;

        $post = $this->postRepository->updateFromDto($postDb, $updatePostDto);

        if ($updatePostDto->categoryIds !== null) {
            $post->categories()->sync($updatePostDto->categoryIds);

            activity()
                ->performedOn($post)
                ->causedBy($user)
                ->withProperties([
                    'old_categories' => $oldCategoryIds,
                    'new_categories' => $newCategoryIds,
                ])
                ->log('Post categories updated');
        }


        if ($updatePostDto->coverImage && $updatePostDto->coverImage->isValid()) {
            $post->addMedia($updatePostDto->coverImage->getPathname())
                ->toMediaCollection('cover_images');

            activity()
                ->performedOn($post)
                ->causedBy($user)
                ->log('Post cover image updated');
        }

        $this->clearCache($post);
        return $post;
    }

    public function getRawPostById(int $id): Post
    {
        return $this->postRepository->getById($id);
    }

    public function getRawPostBySlug(string $slug): Post
    {
        return $this->postRepository->getBySlug($slug);
    }

    public function delete(int $id): void
    {
        $this->postRepository->delete($id);
        $this->clearCache();
    }

    public function getAllForAdmin(): Collection
    {
        return CacheFacade::remember(Cache::KEY_POSTS_ADMIN_ALL, Cache::getTTL('medium'), function () {
            return $this->postRepository->getAllForAdmin();
        });
    }

    public function getMyPostsWithPagination(User $user, int $perPage = 15, int $page = 1): LengthAwarePaginator
    {
        return $this->postRepository->getMyPostsWithPagination($user->id, $perPage, $page);
    }

    /**
     * we need to make sure that the slug is unique
     * and if it is not unique, we need to add a counter to the slug
     * and return the unique slug
     */
    private function generateUniqueSlug(string $title): string
    {
        $baseSlug = Str::slug($title);
        $slug = $baseSlug;
        $counter = 1;

        while ($this->postRepository->isSlugExists($slug)) {
            $counter++;
            $slug = $baseSlug . '-' . $counter;
        }

        return $slug;
    }

    private function determineStatus(User $user): PostStatus
    {
        return $user->isAdmin() ? PostStatus::PUBLISHED : PostStatus::DRAFT;
    }

    private function clearCache(?Post $post = null): void
    {
        CacheFacade::forget(Cache::KEY_POSTS_ALL);
        CacheFacade::forget(Cache::KEY_POSTS_ADMIN_ALL);
        
        $keysToClear = [
            Cache::getPostKey('all'),
            Cache::getPostKey('paginated_15'), // Most common pagination
        ];
        
        if ($post) {
            $keysToClear[] = Cache::getPostKey("id_{$post->id}");
            $keysToClear[] = Cache::getPostKey("slug_{$post->slug}");
        }
 
        foreach ($keysToClear as $key) {
            CacheFacade::forget($key);
        }
    }
}
