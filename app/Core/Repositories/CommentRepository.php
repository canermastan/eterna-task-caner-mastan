<?php

namespace App\Core\Repositories;

use App\Core\Contracts\CommentRepositoryInterface;
use App\Core\Data\Dtos\Comment\CreateCommentDto;
use App\Core\Data\Dtos\Comment\UpdateCommentDto;
use App\Core\Data\Dtos\Comment\CommentModerationDto;
use App\Core\Enums\CommentStatus;
use App\Models\Comment;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class CommentRepository implements CommentRepositoryInterface
{
    public function __construct(
        private Comment $model
    ) {}

    public function getAllWithPagination(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        $query = $this->model->newQuery()->with([
            'user', 
            'parent',
            'post',
            'children' => function($q) {
                $q->where('status', 'approved')->with('user')->orderBy('created_at', 'asc');
            }
        ]);

        if (isset($filters['post_id'])) {
            $query->where('post_id', $filters['post_id']);
        }

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        if (isset($filters['parent_id'])) {
            if ($filters['parent_id'] === 'null') {
                $query->whereNull('parent_id');
            } else {
                $query->where('parent_id', $filters['parent_id']);
            }
        }

        if (isset($filters['search'])) {
            $query->where('content', 'like', '%' . $filters['search'] . '%');
        }

        return $query->orderBy('created_at', 'desc')
                    ->paginate($perPage);
    }

    public function findById(int $id): Comment
    {
        return $this->model->findOrFail($id);
    }

    public function findByIdWithRelations(int $id, array $relations = []): ?Comment
    {
        $defaultRelations = ['user', 'parent', 'children.user'];
        $relations = empty($relations) ? $defaultRelations : $relations;

        return $this->model->with($relations)->findOrFail($id);
    }

    public function createFromDto(CreateCommentDto $createDto, int $userId, string $status): Comment
    {
        return $this->model->create([
            'user_id' => $userId,
            'post_id' => $createDto->postId,
            'content' => $createDto->content,
            'status' => $status,
            'parent_id' => $createDto->parentId,
        ]) ?? throw new \Exception('Failed to create comment');
    }

    public function updateFromDto(Comment $comment, UpdateCommentDto $updateDto): Comment
    {
        $updateData = $updateDto->toUpdateArray();
        
        if (isset($updateData['content'])) {
            $updateData['content'] = $updateData['content'];
        }
        
        $comment->update($updateData);
        return $comment->fresh();
    }

    public function delete(Comment $comment): bool
    {
        return $comment->delete();
    }

    public function moderateComment(Comment $comment, CommentStatus $status): Comment
    {
        $comment->update(['status' => $status]);
        return $comment->fresh(['user', 'parent']);
    }
}
