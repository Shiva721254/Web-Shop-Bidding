<?php

namespace App\Services\Interfaces;

use App\Models\Product;

interface IProductService
{
    /** @return array{data: Product[], total: int, page: int, limit: int} */
    public function getAll(array $filters = [], int $page = 1, int $limit = 10): array;
    public function getById(int $id): ?Product;
    public function create(Product $product): Product;
    public function update(Product $product): bool;
    public function delete(int $id): bool;
}
