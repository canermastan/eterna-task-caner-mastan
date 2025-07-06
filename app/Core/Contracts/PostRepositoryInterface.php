<?php

namespace App\Core\Contracts;

use App\Core\Data\Dtos\Post\TogglePublishStatusDto;
use App\Core\Data\Dtos\Post\UpdatePostDto;
use App\Models\Post;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface PostRepositoryInterface
{
    public function create(array $data): Post;

    public function getById(int $id): Post;

    public function getBySlug(string $slug): Post;

    public function getByIdWithUserAndCategory(int $id): Post;

    public function getBySlugWithUserAndCategory(string $slug): Post;

    public function getAll(): Collection;

    public function getAllWithPagination(int $perPage = 15): LengthAwarePaginator;

    public function getAllForAdmin(): Collection;
    
    public function getMyPostsWithPagination(int $userId, int $perPage = 15): LengthAwarePaginator;

    public function toggleDraftPublished(TogglePublishStatusDto $dto): Post;

    public function updateFromDto(Post $post, UpdatePostDto $updatePostDto): Post;

    public function delete(int $id): void;

    public function isSlugExists(string $slug): bool;
}
