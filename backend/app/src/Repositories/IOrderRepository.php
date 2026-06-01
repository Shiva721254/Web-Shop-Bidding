<?php

namespace App\Repositories;

use App\Models\Order;

interface IOrderRepository
{
    /** @return Order[] */
    public function getAll(int $page = 1, int $limit = 10): array;
    public function countAll(): int;
    public function getByUser(int $userId, int $page = 1, int $limit = 10): array;
    public function countByUser(int $userId): int;
    public function getById(int $id): ?Order;
    public function create(Order $order, array $items): Order;
    public function updateStatus(int $id, string $status): bool;
}
