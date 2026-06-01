<?php

namespace App\Models;

class Product
{
    public ?int    $id           = null;
    public string  $title        = '';
    public string  $description  = '';
    public string  $category     = '';
    public string  $type         = 'buy_now';
    public ?float  $price        = null;
    public ?float  $startingPrice = null;
    public ?int    $sellerId     = null;
    public ?string $createdAt    = null;

    public function __construct(array $data = [])
    {
        $this->id            = isset($data['id'])             ? (int)$data['id']              : null;
        $this->title         = $data['title']                 ?? '';
        $this->description   = $data['description']           ?? '';
        $this->category      = $data['category']              ?? '';
        $this->type          = $data['type']                  ?? 'buy_now';
        $this->price         = isset($data['price'])          ? (float)$data['price']         : null;
        $this->startingPrice = isset($data['starting_price']) ? (float)$data['starting_price'] : null;
        $this->sellerId      = isset($data['seller_id'])      ? (int)$data['seller_id']       : null;
        $this->createdAt     = $data['created_at']            ?? null;
    }
}
