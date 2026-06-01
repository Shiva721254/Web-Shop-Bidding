<?php

namespace App\Models;

class Auction
{
    public ?int $id;
    public string $title;
    public string $seller;
    public string $category;
    public string $endsAt;
    public string $description;
    public float $startingPrice;
    public float $currentBid;
    public string $status;

    public function __construct(array $data = [])
    {
        $this->id = $data['id'] ?? null;
        $this->title = $data['title'] ?? '';
        $this->seller = $data['seller'] ?? '';
        $this->category = $data['category'] ?? '';
        $this->endsAt = $data['endsAt'] ?? '';
        $this->description = $data['description'] ?? '';
        $this->startingPrice = (float)($data['startingPrice'] ?? 0);
        $this->currentBid = (float)($data['currentBid'] ?? $this->startingPrice);
        $this->status = $data['status'] ?? 'open';
    }
}
