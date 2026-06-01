<?php

namespace App\Models;

class Auction
{
    public ?int    $id           = null;
    public ?int    $productId    = null;
    public string  $title        = '';
    public string  $description  = '';
    public string  $category     = '';
    public ?int    $sellerId     = null;
    public float   $startingPrice = 0.0;
    public ?float  $currentBid   = null;
    public string  $endsAt       = '';
    public string  $status       = 'open';
    public ?int    $winnerId     = null;

    public function __construct(array $data = [])
    {
        $this->id            = isset($data['id'])             ? (int)$data['id']            : null;
        $this->productId     = isset($data['product_id'])     ? (int)$data['product_id']    : null;
        $this->title         = $data['title']                 ?? '';
        $this->description   = $data['description']           ?? '';
        $this->category      = $data['category']              ?? '';
        $this->sellerId      = isset($data['seller_id'])      ? (int)$data['seller_id']     : null;
        $this->startingPrice = (float)($data['starting_price'] ?? 0);
        $this->currentBid    = isset($data['current_bid'])    ? (float)$data['current_bid'] : null;
        $this->endsAt        = $data['ends_at']               ?? '';
        $this->status        = $data['status']                ?? 'open';
        $this->winnerId      = isset($data['winner_id'])      ? (int)$data['winner_id']     : null;
    }
}
