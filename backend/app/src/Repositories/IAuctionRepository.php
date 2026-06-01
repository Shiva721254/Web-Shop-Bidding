<?php

namespace App\Repositories;

use App\Models\Auction;

interface IAuctionRepository
{
    /** @return Auction[] */
    public function getAll(array $filters = [], int $page = 1, int $limit = 10): array;
    public function countAll(array $filters = []): int;
    public function getById(int $id): ?Auction;
    public function create(Auction $auction): Auction;
    public function update(Auction $auction): bool;
    public function delete(int $id): bool;
}
