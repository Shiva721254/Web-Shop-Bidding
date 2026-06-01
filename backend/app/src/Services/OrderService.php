<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Repositories\IOrderRepository;
use App\Repositories\OrderRepository;
use App\Repositories\IProductRepository;
use App\Repositories\ProductRepository;

class OrderService implements IOrderService
{
    private IOrderRepository   $orderRepository;
    private IProductRepository $productRepository;

    public function __construct()
    {
        $this->orderRepository   = new OrderRepository();
        $this->productRepository = new ProductRepository();
    }

    public function getAll(int $page = 1, int $limit = 10): array
    {
        return [
            'data'  => $this->orderRepository->getAll($page, $limit),
            'total' => $this->orderRepository->countAll(),
            'page'  => $page,
            'limit' => $limit,
        ];
    }

    public function getByUser(int $userId, int $page = 1, int $limit = 10): array
    {
        return [
            'data'  => $this->orderRepository->getByUser($userId, $page, $limit),
            'total' => $this->orderRepository->countByUser($userId),
            'page'  => $page,
            'limit' => $limit,
        ];
    }

    public function getById(int $id): ?Order
    {
        return $this->orderRepository->getById($id);
    }

    public function createFromItems(int $userId, array $rawItems): Order
    {
        if (empty($rawItems)) {
            throw new \InvalidArgumentException('Order must contain at least one item', 422);
        }

        $orderItems = [];
        $total      = 0.0;

        foreach ($rawItems as $raw) {
            $productId = isset($raw['productId']) ? (int)$raw['productId'] : 0;
            $quantity  = isset($raw['quantity'])  ? (int)$raw['quantity']  : 1;

            if ($productId <= 0) {
                throw new \InvalidArgumentException('Each item must have a valid productId', 422);
            }
            if ($quantity <= 0) {
                throw new \InvalidArgumentException('Quantity must be at least 1', 422);
            }

            $product = $this->productRepository->getById($productId);
            if (!$product) {
                throw new \InvalidArgumentException("Product {$productId} not found", 404);
            }
            if ($product->type !== 'buy_now' || $product->price === null) {
                throw new \InvalidArgumentException(
                    "Product {$productId} is not available for direct purchase", 422
                );
            }

            $item                  = new OrderItem();
            $item->productId       = $productId;
            $item->quantity        = $quantity;
            $item->priceAtPurchase = $product->price;

            $total       += $product->price * $quantity;
            $orderItems[] = $item;
        }

        $order         = new Order();
        $order->userId = $userId;
        $order->total  = round($total, 2);
        $order->status = 'confirmed';

        return $this->orderRepository->create($order, $orderItems);
    }

    public function updateStatus(int $id, string $status): bool
    {
        $allowed = ['pending', 'confirmed', 'cancelled'];
        if (!in_array($status, $allowed, true)) {
            throw new \InvalidArgumentException(
                'Status must be one of: ' . implode(', ', $allowed), 422
            );
        }
        return $this->orderRepository->updateStatus($id, $status);
    }
}
