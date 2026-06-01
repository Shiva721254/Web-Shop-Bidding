<?php

namespace App\Models;

class OrderItem
{
    public ?int    $id               = null;
    public int     $orderId          = 0;
    public int     $productId        = 0;
    public int     $quantity         = 1;
    public float   $priceAtPurchase  = 0.0;
    public ?string $createdAt        = null;

    public function __construct(array $data = [])
    {
        $this->id              = isset($data['id'])                ? (int)$data['id']                  : null;
        $this->orderId         = isset($data['order_id'])          ? (int)$data['order_id']             : 0;
        $this->productId       = isset($data['product_id'])        ? (int)$data['product_id']           : 0;
        $this->quantity        = isset($data['quantity'])          ? (int)$data['quantity']             : 1;
        $this->priceAtPurchase = isset($data['price_at_purchase']) ? (float)$data['price_at_purchase']  : 0.0;
        $this->createdAt       = $data['created_at']               ?? null;
    }
}
