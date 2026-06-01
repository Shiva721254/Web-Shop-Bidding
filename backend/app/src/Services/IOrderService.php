<?php

namespace App\Services;

use App\Models\Order;

interface IOrderService
{
    /** @return array{data: Order[], total: int, page: int, limit: int} */
    public function getAll(int $page = 1, int $limit = 10): array;
    public function getByUser(int $userId, int $page = 1, int $limit = 10): array;
    public function getById(int $id): ?Order;
    public function createFromItems(int $userId, array $items): Order;
    public function updateStatus(int $id, string $status): bool;
}
