<?php

namespace App\Core\Repositories;

use App\Core\Contracts\PostRepositoryInterface;
use App\Core\Data\Dtos\Post\TogglePublishStatusDto;
use App\Core\Data\Dtos\Post\UpdatePostDto;
use App\Models\Post;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class PostRepository implements PostRepositoryInterface
{
    public function __construct(
        private Post $model
    ) {}

    public function create(array $data): Post
    {
        return $this->model->create($data) ?? throw new \Exception('Failed to create post');
    }

    public function getById(int $id): Post
    {
        return $this->model->with(['categories', 'user'])->findOrFail($id);
    }

    public function getBySlug(string $slug): Post
    {
        return $this->model->with(['categories', 'user'])->where('slug', $slug)->firstOrFail();
    }

    public function getByIdWithUserAndCategory(int $id): Post
    {
        return $this->model->with(['categories', 'user'])->findOrFail($id);
    }

    public function getBySlugWithUserAndCategory(string $slug): Post
    {
        return $this->model->with(['categories', 'user'])->where('slug', $slug)->firstOrFail();
    }

    public function getAll(): Collection
    {
        return $this->model->with(['categories', 'user'])->get();
    }

    public function getAllWithPagination(int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->with(['categories', 'user'])
                          ->published()
                          ->orderBy('published_at', 'desc')
                          ->paginate($perPage);
    }

    public function getByUserIdWithPagination(int $userId): LengthAwarePaginator
    {
        return $this->model->with(['categories', 'user'])
                          ->where('user_id', $userId)
                          ->orderBy('created_at', 'desc')
                          ->paginate(10);
    }   

    public function getAllForAdmin(): Collection
    {
        return $this->model->with(['categories', 'user'])
                          ->orderBy('created_at', 'desc')
                          ->get();
    }

    public function getMyPostsWithPagination(int $userId, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->with(['categories', 'user'])
                          ->where('user_id', $userId)
                          ->orderBy('created_at', 'desc')
                          ->paginate($perPage);
    }

    public function toggleDraftPublished(TogglePublishStatusDto $dto): Post
    {
        $post = $this->getById($dto->id);
        $post->update([
            'status' => $dto->status,
            'published_at' => $dto->publishedAt,
        ]);
        return $post;
    }

    public function updateFromDto(Post $post, UpdatePostDto $updatePostDto): Post
    {
        $updateData = $updatePostDto->toUpdateArray();

        if (!empty($updateData)) {
            $post->update($updateData);
        }

        return $post;
    }

    public function delete(int $id): void
    {
        $this->getById($id)->delete();
    }

    public function isSlugExists(string $slug): bool
    {
        return $this->model->where('slug', $slug)->exists();
    }
}
