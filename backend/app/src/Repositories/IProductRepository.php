<?php

namespace App\Repositories;

use App\Models\Product;

interface IProductRepository
{
    /** @return Product[] */
    public function getAll(array $filters = [], int $page = 1, int $limit = 10): array;
    public function countAll(array $filters = []): int;
    public function getById(int $id): ?Product;
    public function create(Product $product): Product;
    public function update(Product $product): bool;
    public function delete(int $id): bool;
}
