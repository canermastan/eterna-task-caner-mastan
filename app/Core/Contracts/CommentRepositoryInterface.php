<?php

namespace App\Core\Contracts;

use App\Core\Data\Dtos\Comment\CreateCommentDto;
use App\Core\Data\Dtos\Comment\UpdateCommentDto;
use App\Core\Enums\CommentStatus;
use App\Models\Comment;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface CommentRepositoryInterface
{
    public function createFromDto(CreateCommentDto $createDto, int $userId, string $status): Comment;

    public function updateFromDto(Comment $comment, UpdateCommentDto $updateDto): Comment;

    public function delete(Comment $comment): bool;

    public function moderateComment(Comment $comment, CommentStatus $status): Comment;

    public function getAllWithPagination(int $perPage = 15, array $filters = []): LengthAwarePaginator;
    
    public function findById(int $id): Comment;

    public function findByIdWithRelations(int $id, array $relations = []): ?Comment;
}
