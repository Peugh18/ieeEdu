<?php

namespace App\Services;

use App\Models\Category;
use App\Repositories\CategoryRepository;

class CategoryService
{
    public function __construct(protected CategoryRepository $repo)
    {
    }

    public function list($perPage = 15)
    {
        return $this->repo->paginate($perPage);
    }

    public function create(array $data): Category
    {
        return $this->repo->create($data);
    }

    public function update(Category $category, array $data): Category
    {
        return $this->repo->update($category, $data);
    }

    public function delete(Category $category)
    {
        return $this->repo->delete($category);
    }
}
