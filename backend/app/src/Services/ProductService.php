<?php

namespace App\Services;

use App\Models\Product;
use App\Repositories\IProductRepository;
use App\Repositories\ProductRepository;

class ProductService implements IProductService
{
    private IProductRepository $repository;

    public function __construct()
    {
        $this->repository = new ProductRepository();
    }

    public function getAll(array $filters = [], int $page = 1, int $limit = 10): array
    {
        return [
            'data'  => $this->repository->getAll($filters, $page, $limit),
            'total' => $this->repository->countAll($filters),
            'page'  => $page,
            'limit' => $limit,
        ];
    }

    public function getById(int $id): ?Product
    {
        return $this->repository->getById($id);
    }

    public function create(Product $product): Product
    {
        return $this->repository->create($product);
    }

    public function update(Product $product): bool
    {
        return $this->repository->update($product);
    }

    public function delete(int $id): bool
    {
        return $this->repository->delete($id);
    }
}
