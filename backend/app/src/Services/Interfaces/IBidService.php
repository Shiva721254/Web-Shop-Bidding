<?php

namespace App\Services\Interfaces;

use App\Models\Bid;

interface IBidService
{
    /** @return array{data: Bid[], total: int, page: int, limit: int} */
    public function getByAuction(int $auctionId, int $page = 1, int $limit = 10): array;
    public function placeBid(int $auctionId, int $userId, float $amount): Bid;
}
