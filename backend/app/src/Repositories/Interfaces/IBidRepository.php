<?php

namespace App\Repositories\Interfaces;

use App\Models\Bid;

interface IBidRepository
{
    /** @return Bid[] */
    public function getByAuction(int $auctionId, int $page = 1, int $limit = 10): array;
    public function countByAuction(int $auctionId): int;
    public function getById(int $id): ?Bid;
    public function create(Bid $bid): Bid;
    public function updateAuctionCurrentBid(int $auctionId, float $amount): void;
}
