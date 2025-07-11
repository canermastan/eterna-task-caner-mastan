<?php

namespace App\Http\Controllers\Api;

use App\Core\Data\Requests\Category\StoreCategoryRequest;
use App\Core\Data\Requests\Category\UpdateCategoryRequest;
use App\Core\Data\Resources\CategoryResource;
use App\Core\Services\CategoryService;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Traits\ApiResponseTrait;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class CategoryController extends Controller
{
    use ApiResponseTrait, AuthorizesRequests;

    public function __construct(
        private CategoryService $categoryService
    ) {}

    public function index(): JsonResponse
    {
        $categories = $this->categoryService->getAll();
        return $this->successResponse(CategoryResource::collection($categories), 'Categories fetched successfully', Response::HTTP_OK);
    }

    public function store(StoreCategoryRequest $request): JsonResponse
    {
        $this->authorize('create', Category::class);

        $category = $this->categoryService->create($request->validated()['name']);
        return $this->successResponse(new CategoryResource($category), 'Category created successfully', Response::HTTP_CREATED);
    }

    public function update(UpdateCategoryRequest $request, int $id): JsonResponse
    {
        $this->authorize('update', Category::class);

        $category = $this->categoryService->update($id, $request->validated()['name']);
        return $this->successResponse(new CategoryResource($category), 'Category updated successfully', Response::HTTP_OK);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->authorize('delete', Category::class);

        $this->categoryService->delete($id);
        return $this->successResponse(null, 'Category deleted successfully', Response::HTTP_NO_CONTENT);
    }
}
