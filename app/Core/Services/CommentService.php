<?php

namespace App\Core\Services;

use App\Core\Contracts\CommentRepositoryInterface;
use App\Core\Contracts\PostRepositoryInterface;
use App\Core\Data\Dtos\Comment\CreateCommentDto;
use App\Core\Data\Dtos\Comment\UpdateCommentDto;
use App\Core\Data\Resources\CommentResource;
use App\Core\Enums\CommentStatus;
use App\Events\CommentCreated;
use App\Events\CommentDeleted;
use App\Events\CommentUpdated;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;

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

    public function createComment(CreateCommentDto $createDto, User $user): CommentResource
    {
        $post = $this->postRepository->getById($createDto->postId);

        $status = $this->determineInitialStatus($user);
        $comment = $this->commentRepository->createFromDto($createDto, $user->id, $status);

        if ($comment->status === 'approved') {
            $comment->load(['user']);
            CommentCreated::dispatch($comment, $post);
        }

        $this->clearCommentsCache();

        return new CommentResource($comment->load(['user', 'parent']));
    }

    public function updateComment(int $commentId, UpdateCommentDto $updateDto): CommentResource
    {
        $comment = $this->commentRepository->findByIdWithRelations($commentId);

        $updatedComment = $this->commentRepository->updateFromDto($comment, $updateDto);

        $updatedComment->load(['user', 'post']);
        CommentUpdated::dispatch($updatedComment, $updatedComment->post);

        $this->clearCommentsCache();

        return new CommentResource($updatedComment->load(['user', 'parent']));
    }

    public function deleteComment(int $commentId): bool
    {
        $comment = $this->commentRepository->findByIdWithRelations($commentId);

        $result = $this->commentRepository->delete($comment);

        if ($result) {
            $comment->load(['user', 'post']);
            CommentDeleted::dispatch($comment->id, $comment->post->id);

            $this->clearCommentsCache();
        }

        return $result;
    }

    public function moderateComment(int $commentId, CommentStatus $status): CommentResource
    {
        $comment = $this->commentRepository->findById($commentId);

        $moderatedComment = $this->commentRepository->moderateComment($comment, $status);

        $moderatedComment->load(['user', 'post']);

        if ($status === CommentStatus::APPROVED) {
            CommentCreated::dispatch($moderatedComment, $moderatedComment->post);
        } else {
            CommentDeleted::dispatch($moderatedComment->id, $moderatedComment->post->id);
        }

        $this->clearCommentsCache();
        return new CommentResource($moderatedComment);
    }

    public function getPostCommentsWithPagination(int $postId, int $perPage = 15, array $additionalFilters = []): LengthAwarePaginator
    {
        $filters = array_merge([
            'post_id' => $postId,
            'parent_id' => 'null'
        ], $additionalFilters);

        return $this->commentRepository->getAllWithPagination($perPage, $filters);
    }

    private function determineInitialStatus(User $user): string
    {
        return $user->isAdmin() ? 'approved' : 'pending';
    }

    private function clearCommentsCache(): void
    {
        Cache::forget('approved_comments');

        try {
            Cache::tags(['comments'])->flush();
        } catch (\BadMethodCallException $e) {
            $this->clearIndividualCacheKeys();
        }
    }

    private function clearIndividualCacheKeys(): void
    {
        $patterns = [
            'approved_comments',
            'comments_*',
            'admin_comments_*',
            'post_comments_*',
        ];

        foreach ($patterns as $pattern) {
            Cache::forget($pattern);
        }
    }
}
