<?php

namespace App\Services;

use App\Models\Auction;
use App\Repositories\Interfaces\IAuctionRepository;
use App\Repositories\AuctionRepository;
use App\Services\Interfaces\IAuctionService;

class AuctionService implements IAuctionService
{
    private IAuctionRepository $repository;

    public function __construct()
    {
        $this->repository = new AuctionRepository();
    }

    public function getAll(array $filters = [], int $page = 1, int $limit = 10): array
    {
        $data  = $this->repository->getAll($filters, $page, $limit);
        $total = $this->repository->countAll($filters);

        return [
            'data'  => $data,
            'total' => $total,
            'page'  => $page,
            'limit' => $limit,
        ];
    }

    public function getById(int $id): ?Auction
    {
        return $this->repository->getById($id);
    }

    public function create(Auction $auction): Auction
    {
        return $this->repository->create($auction);
    }

    public function update(Auction $auction): bool
    {
        return $this->repository->update($auction);
    }

    public function delete(int $id): bool
    {
        return $this->repository->delete($id);
    }
}
