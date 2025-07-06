<?php

namespace App\Core\Services;

use App\Core\Constants\Cache;
use App\Core\Contracts\CategoryRepositoryInterface;
use App\Core\Data\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache as CacheFacade;
use Illuminate\Support\Str;

class CategoryService
{
    public function __construct(
        private CategoryRepositoryInterface $categoryRepository
    ) {}

    public function getAll(): Collection
    {
        return CacheFacade::remember(Cache::KEY_CATEGORIES_ALL, Cache::getTTL('very_long'), function () {
            return $this->categoryRepository->getAll();
        });
    }

    public function create(string $name): Category
    {
        $category = $this->categoryRepository->create([
            'name' => $name,
            'slug' => Str::slug($name),
        ]);
        
        $this->clearCache();
        return $category;
    }

    public function update(int $id, string $name): Category
    {
        $category = $this->categoryRepository->update($id, [
            'name' => $name,
            'slug' => Str::slug($name),
        ]);
        
        $this->clearCache();
        return $category;
    }

    public function delete(int $id): void
    {
        $this->categoryRepository->delete($id);
        $this->clearCache();
    }

    private function clearCache(): void
    {
        CacheFacade::forget(Cache::KEY_CATEGORIES_ALL);
    }
}