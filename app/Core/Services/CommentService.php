<?php

namespace App\Core\Services;

use App\Core\Contracts\CommentRepositoryInterface;
use App\Core\Contracts\PostRepositoryInterface;
use App\Core\Data\Dtos\Comment\CreateCommentDto;
use App\Core\Data\Dtos\Comment\UpdateCommentDto;
use App\Core\Enums\CommentStatus;
use App\Events\CommentCreated;
use App\Events\CommentDeleted;
use App\Events\CommentUpdated;
use App\Models\Comment;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class CommentService
{
    public function __construct(
        private CommentRepositoryInterface $commentRepository,
        private PostRepositoryInterface $postRepository
    ) {}

    public function getPaginatedComments(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        return $this->commentRepository->getAllWithPagination($perPage, $filters);
    }

    public function createComment(CreateCommentDto $createDto, User $user): Comment
    {
        $post = $this->postRepository->getById($createDto->postId);

        $status = $this->determineInitialStatus($user);
        $comment = $this->commentRepository->createFromDto($createDto, $user->id, $status);

        $comment->load(['user', 'parent']);
        if ($comment->status === 'approved') {
            CommentCreated::dispatch($comment, $post);
        }

        return $comment;
    }

    public function updateComment(int $commentId, UpdateCommentDto $updateDto): Comment
    {
        $comment = $this->commentRepository->findByIdWithRelations($commentId);

        $updatedComment = $this->commentRepository->updateFromDto($comment, $updateDto);

        $updatedComment->load(['user', 'post']);
        CommentUpdated::dispatch($updatedComment, $updatedComment->post);

        return $updatedComment;
    }

    public function deleteComment(int $commentId): bool
    {
        $comment = $this->commentRepository->findByIdWithRelations($commentId);

        $result = $this->commentRepository->delete($comment);

        if ($result) {
            $comment->load(['user', 'post']);
            CommentDeleted::dispatch($comment->id, $comment->post->id);
        }

        return $result;
    }

    public function moderateComment(int $commentId, CommentStatus $status): Comment
    {
        $comment = $this->commentRepository->findById($commentId);

        $moderatedComment = $this->commentRepository->moderateComment($comment, $status);

        $moderatedComment->load(['user', 'post']);

        if ($status === CommentStatus::APPROVED) {
            CommentCreated::dispatch($moderatedComment, $moderatedComment->post);
        } else {
            CommentDeleted::dispatch($moderatedComment->id, $moderatedComment->post->id);
        }

        return $moderatedComment;
    }

    public function getPostCommentsWithPagination(int $postId, ?User $user, int $perPage = 15, array $additionalFilters = []): LengthAwarePaginator
    {
        $filters = [
            'post_id' => $postId,
            'parent_id' => 'null'
        ];

        if ($user && $user->can('filter', Comment::class)) {
            $filters = array_merge($filters, $additionalFilters);
        } else {
            $filters['status'] = 'approved';
        }

        return $this->commentRepository->getAllWithPagination($perPage, $filters);
    }

    private function determineInitialStatus(User $user): string
    {
        return $user->isAdmin() ? 'approved' : 'pending';
    }
}
