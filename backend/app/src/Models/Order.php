<?php

namespace App\Models;

class Order
{
    public ?int    $id        = null;
    public int     $userId    = 0;
    public float   $total     = 0.0;
    public string  $status    = 'pending';
    public ?string $createdAt = null;

    /** @var OrderItem[] */
    public array $items = [];

    public function __construct(array $data = [])
    {
        $this->id        = isset($data['id'])      ? (int)$data['id']     : null;
        $this->userId    = isset($data['user_id']) ? (int)$data['user_id'] : 0;
        $this->total     = isset($data['total'])   ? (float)$data['total'] : 0.0;
        $this->status    = $data['status']         ?? 'pending';
        $this->createdAt = $data['created_at']     ?? null;
    }
}
