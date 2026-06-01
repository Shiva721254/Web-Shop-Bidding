<?php

namespace App\Services;

use App\Models\Auction;

interface IAuctionService
{
    /**
     * @return Auction[]
     */
    public function getAll(): array;
    public function getById(int $id): ?Auction;
    public function create(Auction $auction): Auction;
    public function update(Auction $auction): bool;
    public function delete(int $id): bool;
}
