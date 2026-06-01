<?php

namespace App\Services;

use App\Models\Auction;

interface IAuctionService
{
    /** @return array{data: Auction[], total: int, page: int, limit: int} */
    public function getAll(array $filters = [], int $page = 1, int $limit = 10): array;
    public function getById(int $id): ?Auction;
    public function create(Auction $auction): Auction;
    public function update(Auction $auction): bool;
    public function delete(int $id): bool;
}
