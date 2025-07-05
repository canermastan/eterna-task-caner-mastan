<?php

namespace App\Core\Services;

use App\Core\Constants\Cache;
use App\Core\Contracts\PostRepositoryInterface;
use App\Core\Data\Dtos\Post\CreatePostDto;
use App\Core\Data\Dtos\Post\TogglePublishStatusDto;
use App\Core\Data\Dtos\Post\UpdatePostDto;
use App\Core\Data\Resources\PostResource;
use App\Core\Enums\PostStatus;
use App\Events\PostPublished;
use App\Events\PostStatusUpdated;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Cache as CacheFacade;
use Illuminate\Support\Str;

class PostService
{
    public function __construct(
        private PostRepositoryInterface $postRepository
    ) {}

    public function getAll(): AnonymousResourceCollection
    {
        return CacheFacade::remember(Cache::getPostKey('all'), Cache::getTTL('long'), function () {
                $posts = $this->postRepository->getAll();
                return PostResource::collection($posts);
            }
        );
    }

    public function getAllWithPagination(int $perPage = 15): AnonymousResourceCollection
    {
        $cacheKey = Cache::getPostKey("paginated_{$perPage}");
        return CacheFacade::remember($cacheKey, Cache::getTTL('medium'), function () use ($perPage) {
            $posts = $this->postRepository->getAllWithPagination($perPage);
            return PostResource::collection($posts);
        });
    }

    public function getById(int $id): PostResource
    {
        return CacheFacade::remember(Cache::getPostKey("id_{$id}"), Cache::getTTL('long'), function () use ($id) {
            $post = $this->postRepository->getByIdWithUserAndCategory($id);
            return new PostResource($post);
        });
    }

    public function getBySlug(string $slug): PostResource
    {
        return CacheFacade::remember(Cache::getPostKey("slug_{$slug}"), Cache::getTTL('long'), function () use ($slug) {    
            $post = $this->postRepository->getBySlugWithUserAndCategory($slug);
            return new PostResource($post);
        });
    }

    public function createPost(CreatePostDto $postDto, User $user): PostResource
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
        }

        if ($postDto->coverImage && $postDto->coverImage->isValid()) {
            $post->addMedia($postDto->coverImage->getPathname())
                ->toMediaCollection('cover_images');
        }

        $post->load(['user', 'categories']);

        if ($status === PostStatus::PUBLISHED) {
            PostPublished::dispatch($post);
        }

        $this->clearCache();
        return new PostResource($post);
    }

    public function toggleDraftPublished(int $postId): PostResource
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

        $this->clearCache();
        return new PostResource($updatedPost);
    }

    public function update(int $id, UpdatePostDto $updatePostDto, User $user): PostResource
    {
        // if user is a writer, we need to set the status to draft again and set the published_at to null
        $updatePostDto->status = $this->determineStatus($user);
        $updatePostDto->published_at = $updatePostDto->status === PostStatus::DRAFT ? null : now();

        $postDb = $this->postRepository->getById($id);
        $post = $this->postRepository->updateFromDto($postDb, $updatePostDto);

        if ($updatePostDto->coverImage && $updatePostDto->coverImage->isValid()) {
            $post->addMedia($updatePostDto->coverImage->getPathname())
                ->toMediaCollection('cover_images');
        }

        $this->clearCache();
        return new PostResource($post);
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

    public function getByUserIdWithPagination(int $userId): AnonymousResourceCollection
    {
        $posts = $this->postRepository->getByUserIdWithPagination($userId);
        return PostResource::collection($posts);
    }

    public function getAllForAdmin(): AnonymousResourceCollection
    {
        return CacheFacade::remember(Cache::KEY_POSTS_ADMIN_ALL, Cache::getTTL('medium'), function () {
            $posts = $this->postRepository->getAllForAdmin();
            return PostResource::collection($posts);
        });
    }

    public function getMyPostsWithPagination(User $user, int $perPage = 15): AnonymousResourceCollection
    {
        $posts = $this->postRepository->getMyPostsWithPagination($user->id, $perPage);
        return PostResource::collection($posts);
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

    private function clearCache(): void
    {
        CacheFacade::forget(Cache::KEY_POSTS_ALL);
        CacheFacade::forget(Cache::KEY_POSTS_ADMIN_ALL);
        
        $keysToClear = [
            Cache::getPostKey('all'),
            Cache::getPostKey('paginated_15'), // Most common pagination
        ];
        
        foreach ($keysToClear as $key) {
            CacheFacade::forget($key);
        }
    }
}
