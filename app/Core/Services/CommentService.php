<?php

namespace App\Core\Services;

use App\Core\Constants\Cache;
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
use Illuminate\Support\Facades\Cache as CacheFacade;

class CommentService
{
    public function __construct(
        private CommentRepositoryInterface $commentRepository,
        private PostRepositoryInterface $postRepository
    ) {}

    public function getPaginatedComments(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        $cacheKey = Cache::getCommentKey('paginated_' . $perPage . '_' . md5(serialize($filters)));
        return CacheFacade::remember($cacheKey, Cache::getTTL('medium'), function () use ($perPage, $filters) {
            return $this->commentRepository->getAllWithPagination($perPage, $filters);
        });
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

        $this->clearCommentsCache($createDto->postId);
        return new CommentResource($comment->load(['user', 'parent']));
    }

    public function updateComment(int $commentId, UpdateCommentDto $updateDto): CommentResource
    {
        $comment = $this->commentRepository->findByIdWithRelations($commentId);

        $updatedComment = $this->commentRepository->updateFromDto($comment, $updateDto);

        $updatedComment->load(['user', 'post']);
        CommentUpdated::dispatch($updatedComment, $updatedComment->post);

        $this->clearCommentsCache($updatedComment->post_id);
        return new CommentResource($updatedComment->load(['user', 'parent']));
    }

    public function deleteComment(int $commentId): bool
    {
        $comment = $this->commentRepository->findByIdWithRelations($commentId);

        $result = $this->commentRepository->delete($comment);

        if ($result) {
            $comment->load(['user', 'post']);
            CommentDeleted::dispatch($comment->id, $comment->post->id);

            $this->clearCommentsCache($comment->post_id);
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

        $this->clearCommentsCache($moderatedComment->post_id);
        return new CommentResource($moderatedComment);
    }

    public function getPostCommentsWithPagination(int $postId, int $perPage = 15, array $additionalFilters = []): LengthAwarePaginator
    {
        $filters = array_merge([
            'post_id' => $postId,
            'parent_id' => 'null'
        ], $additionalFilters);

        $cacheKey = Cache::getCommentKey("post_{$postId}_{$perPage}_" . md5(serialize($additionalFilters)));
        return CacheFacade::remember($cacheKey, Cache::getTTL('short'), function () use ($perPage, $filters) {
            return $this->commentRepository->getAllWithPagination($perPage, $filters);
        });
    }

    private function determineInitialStatus(User $user): string
    {
        return $user->isAdmin() ? 'approved' : 'pending';
    }

    private function clearCommentsCache(?int $postId = null): void
    {
        if ($postId) {
            // Clear specific post comments cache
            $postCacheKey = Cache::getCommentKey("post_{$postId}");
            CacheFacade::forget($postCacheKey);
        }
        
        // Clear general comments cache
        CacheFacade::forget(Cache::getCommentKey('paginated'));
        
        // Clear all comment-related cache using tags
        CacheFacade::tags([Cache::TAG_COMMENTS])->flush();
    }
}
