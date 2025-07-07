<?php

namespace App\Http\Controllers\Api;

use App\Core\Data\Dtos\Comment\CreateCommentDto;
use App\Core\Data\Dtos\Comment\UpdateCommentDto;
use App\Core\Data\Dtos\PaginationParametersDto;
use App\Core\Data\Requests\Comment\StoreCommentRequest;
use App\Core\Data\Requests\Comment\UpdateCommentRequest;
use App\Core\Data\Resources\CommentResource;
use App\Core\Enums\CommentStatus;
use App\Core\Services\CommentService;
use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Traits\ApiResponseTrait;
use App\Traits\ValidatesPagination;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CommentController extends Controller
{
    use ApiResponseTrait, AuthorizesRequests;

    public function __construct(
        private CommentService $commentService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAllComments', Comment::class);

        $paginationParameters = PaginationParametersDto::fromRequest($request);
        $filters = $request->only(['status', 'user_id', 'parent_id', 'search']);

        $comments = $this->commentService->getPaginatedComments($paginationParameters->perPage, $filters, $paginationParameters->page);

        return $this->paginatedResponse(CommentResource::collection($comments), "Comments fetched successfully");
    }

    public function store(StoreCommentRequest $request): JsonResponse
    {
        $this->authorize('create', Comment::class);

        $createDto = CreateCommentDto::fromRequest($request);
        $commentResource = $this->commentService->createComment($createDto, $request->user());

        return $this->successResponse($commentResource, 'Comment created successfully', Response::HTTP_CREATED);
    }

    public function update(UpdateCommentRequest $request, Comment $comment): JsonResponse
    {
        $this->authorize('update', $comment);

        $updateDto = UpdateCommentDto::fromRequest($request);
        $commentResource = $this->commentService->updateComment($comment->id, $updateDto);

        return $this->successResponse($commentResource, 'Comment updated successfully', Response::HTTP_OK);
    }

    public function destroy(Comment $comment): JsonResponse
    {
        $this->authorize('delete', $comment);

        $this->commentService->deleteComment($comment->id);

        return $this->successResponse(null, 'Comment deleted successfully', Response::HTTP_NO_CONTENT);
    }

    public function approve(int $commentId): JsonResponse
    {
        $this->authorize('approve', Comment::class);

        $commentResource = $this->commentService->moderateComment($commentId, CommentStatus::APPROVED);

        return $this->successResponse($commentResource, 'Comment approved successfully', Response::HTTP_OK);
    }

    public function reject(int $commentId): JsonResponse
    {
        $this->authorize('reject', Comment::class);

        $commentResource = $this->commentService->moderateComment($commentId, CommentStatus::REJECTED);

        return $this->successResponse($commentResource, 'Comment rejected successfully', Response::HTTP_OK);
    }

    public function getPostComments(int $postId, Request $request): JsonResponse
    {
        $paginationParameters = PaginationParametersDto::fromRequest($request);

        $additionalFilters = $request->only(['status', 'search']);

        $comments = $this->commentService->getPostCommentsWithPagination($postId, $request->user(), $paginationParameters->perPage, $additionalFilters, $paginationParameters->page);

        return $this->paginatedResponse(CommentResource::collection($comments), 'Comments fetched successfully');
    }
}
