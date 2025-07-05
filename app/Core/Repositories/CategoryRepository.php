<?php

namespace App\Core\Repositories;

use App\Core\Contracts\CategoryRepositoryInterface;
use App\Models\Category;
use Illuminate\Database\Eloquent\Collection;

class CategoryRepository implements CategoryRepositoryInterface
{
    public function __construct(
        private Category $model
    ) {}

    public function getAll(): Collection
    {
        return $this->model->all();
    }

    public function create(array $data): Category
    {
        return $this->model->create($data) ?? throw new \Exception('Failed to create category');
    }

    public function update(int $id, array $data): Category
    {
        $category = $this->model->findOrFail($id);
        $category->update($data);
        return $category;
    }

    public function delete(int $id): void
    {
        $this->model->findOrFail($id)->delete();
    }
}