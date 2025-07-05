<?php

namespace App\Core\Services;

use App\Core\Contracts\CategoryRepositoryInterface;
use App\Core\Data\Resources\CategoryResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Str;

class CategoryService
{
    public function __construct(
        private CategoryRepositoryInterface $categoryRepository
    ) {}

    public function getAll(): AnonymousResourceCollection
    {
        return CategoryResource::collection($this->categoryRepository->getAll());
    }

    public function create(string $name): CategoryResource
    {
        $category = $this->categoryRepository->create([
            'name' => $name,
            'slug' => Str::slug($name),
        ]);
        return new CategoryResource($category);
    }

    public function update(int $id, string $name): CategoryResource
    {
        $category = $this->categoryRepository->update($id, [
            'name' => $name,
            'slug' => Str::slug($name),
        ]);
        return new CategoryResource($category);
    }

    public function delete(int $id): void
    {
        $this->categoryRepository->delete($id);
    }
}