<?php

namespace App\Core\Contracts;

use App\Models\Category;
use Illuminate\Database\Eloquent\Collection;

interface CategoryRepositoryInterface
{
    public function getAll(): Collection;
    public function create(array $data): Category;
    public function update(int $id, array $data): Category;
    public function delete(int $id): void;
}