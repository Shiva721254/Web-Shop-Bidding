<?php

namespace App\Repositories\Interfaces;

use App\Models\Auction;

interface IAuctionRepository
{
    public function closeExpired(): void;
    /** @return Auction[] */
    public function getAll(array $filters = [], int $page = 1, int $limit = 10): array;
    public function countAll(array $filters = []): int;
    public function getById(int $id): ?Auction;
    public function create(Auction $auction): Auction;
    public function update(Auction $auction): bool;
    public function delete(int $id): bool;
}
