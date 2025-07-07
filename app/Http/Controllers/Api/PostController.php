<?php

namespace App\Http\Controllers\Api;

use App\Core\Constants\Pagination;
use App\Core\Data\Dtos\Post\CreatePostDto;
use App\Core\Data\Dtos\Post\UpdatePostDto;
use App\Core\Data\Requests\Post\StorePostRequest;
use App\Core\Data\Requests\Post\UpdatePostRequest;
use App\Core\Data\Resources\PostResource;
use App\Core\Services\PostService;
use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Traits\ApiResponseTrait;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PostController extends Controller
{
    use ApiResponseTrait, AuthorizesRequests;

    public function __construct(
        private PostService $postService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $perPage = $this->getValidatedPerPage($request);
        $page = max(1, (int) $request->get('page', 1));
        
        $posts = $this->postService->getAllWithPagination($perPage, $page);
        return $this->paginatedResponse(PostResource::collection($posts), 'Posts fetched successfully', Response::HTTP_OK);
    }

    public function show(int $id): JsonResponse
    {
        $post = $this->postService->getById($id);
        return $this->successResponse(new PostResource($post), 'Post fetched successfully');
    }

    public function showBySlug(string $slug): JsonResponse
    {
        $post = $this->postService->getBySlug($slug);
        return $this->successResponse(new PostResource($post), 'Post fetched successfully');
    }

    public function store(StorePostRequest $request): JsonResponse
    {
        $this->authorize('create', Post::class);

        $dto = CreatePostDto::fromRequest($request);
        $post = $this->postService->createPost($dto, $request->user());

        return $this->successResponse(new PostResource($post), 'Post created successfully', Response::HTTP_CREATED);
    }

    public function toggleDraftPublished(int $id): JsonResponse
    {
        $this->authorize('toggleDraftPublished', Post::class);

        $post = $this->postService->toggleDraftPublished($id);
        return $this->successResponse(new PostResource($post), 'Post status updated successfully', Response::HTTP_OK);
    }

    public function update(int $id, UpdatePostRequest $request): JsonResponse
    {
        $this->authorize('update', $this->postService->getRawPostById($id));

        $dto = UpdatePostDto::fromRequest($request);
        $post = $this->postService->update($id, $dto, $request->user());
        return $this->successResponse(new PostResource($post), 'Post updated successfully', Response::HTTP_OK);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->authorize('delete', $this->postService->getRawPostById($id));

        $this->postService->delete($id);
        return $this->successResponse(null, 'Post deleted successfully', Response::HTTP_NO_CONTENT);
    }

    public function getAllForAdmin(): JsonResponse
    {
        $this->authorize('viewAllPostsForAdmin', Post::class);

        $posts = $this->postService->getAllForAdmin();
        return $this->successResponse(PostResource::collection($posts), 'All posts fetched successfully for admin');
    }

    public function getMyPosts(Request $request): JsonResponse
    {
        $this->authorize('viewMyPosts', Post::class);

        $perPage = $this->getValidatedPerPage($request);
        $page = max(1, (int) $request->get('page', 1));
        $posts = $this->postService->getMyPostsWithPagination($request->user(), $perPage, $page);
        return $this->paginatedResponse(PostResource::collection($posts), 'My posts fetched successfully', Response::HTTP_OK);
    }

    private function getValidatedPerPage(Request $request): int
    {
        $perPage = $request->get('per_page', Pagination::DEFAULT_PER_PAGE);
        return min(max((int) $perPage, Pagination::MIN_PER_PAGE), Pagination::MAX_PER_PAGE);
    }
}
