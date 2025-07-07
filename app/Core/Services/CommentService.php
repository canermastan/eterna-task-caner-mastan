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
        private PostRepositoryInterface $postRepository,
        private NotificationService $notificationService
    ) {}

    public function getPaginatedComments(int $perPage = 15, array $filters = [], int $page = 1): LengthAwarePaginator
    {
        return $this->commentRepository->getAllWithPagination($perPage, $filters, $page);
    }

    public function createComment(CreateCommentDto $createDto, User $user): Comment
    {
        $post = $this->postRepository->getById($createDto->postId);

        $status = $this->determineInitialStatus($user);
        $comment = $this->commentRepository->createFromDto($createDto, $user->id, $status);

        $comment->load(['user', 'parent']);
        
        // if user is admin
        if ($comment->status === 'approved') {
            $this->notificationService->sendCommentNotification($comment, $post, $comment->user);
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
            $this->notificationService->sendCommentNotification($moderatedComment, $moderatedComment->post, $moderatedComment->user);
            CommentCreated::dispatch($moderatedComment, $moderatedComment->post);
        } else {
            CommentUpdated::dispatch($moderatedComment, $moderatedComment->post);
        }

        return $moderatedComment;
    }

    public function getPostCommentsWithPagination(int $postId, ?User $user, int $perPage = 15, array $additionalFilters = [], int $page = 1): LengthAwarePaginator
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

        return $this->commentRepository->getAllWithPagination($perPage, $filters, $page);
    }

    private function determineInitialStatus(User $user): string
    {
        return $user->isAdmin() ? 'approved' : 'pending';
    }
}
