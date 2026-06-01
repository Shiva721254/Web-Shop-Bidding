<?php

namespace App\Services;

use App\Models\Auction;
use App\Repositories\IAuctionRepository;
use App\Repositories\AuctionRepository;

class AuctionService implements IAuctionService
{
    private IAuctionRepository $repository;

    public function __construct()
    {
        $this->repository = new AuctionRepository();
    }

    /**
     * @return Auction[]
     */
    public function getAll(): array
    {
        return $this->repository->getAll();
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
