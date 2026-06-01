<?php

namespace App\Models;

class Bid
{
    public ?int    $id        = null;
    public int     $auctionId = 0;
    public int     $userId    = 0;
    public float   $amount    = 0.0;
    public ?string $createdAt = null;

    public function __construct(array $data = [])
    {
        $this->id        = isset($data['id'])         ? (int)$data['id']         : null;
        $this->auctionId = isset($data['auction_id']) ? (int)$data['auction_id'] : 0;
        $this->userId    = isset($data['user_id'])    ? (int)$data['user_id']    : 0;
        $this->amount    = isset($data['amount'])     ? (float)$data['amount']   : 0.0;
        $this->createdAt = $data['created_at']        ?? null;
    }
}
