<?php

namespace App\Repositories;

use App\Models\Order;
use App\Models\OrderItem;
use App\Framework\Database;
use PDO;

class OrderRepository implements IOrderRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    /** @return Order[] */
    public function getAll(int $page = 1, int $limit = 10): array
    {
        $offset = ($page - 1) * $limit;
        $stmt   = $this->db->prepare(
            'SELECT * FROM orders ORDER BY created_at DESC LIMIT :limit OFFSET :offset'
        );
        $stmt->bindValue(':limit',  $limit,  PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        $orders = array_map(fn($row) => new Order($row), $stmt->fetchAll());
        foreach ($orders as $order) {
            $order->items = $this->getItems($order->id);
        }
        return $orders;
    }

    public function countAll(): int
    {
        return (int)$this->db->query('SELECT COUNT(*) FROM orders')->fetchColumn();
    }

    /** @return Order[] */
    public function getByUser(int $userId, int $page = 1, int $limit = 10): array
    {
        $offset = ($page - 1) * $limit;
        $stmt   = $this->db->prepare(
            'SELECT * FROM orders WHERE user_id = :user_id ORDER BY created_at DESC LIMIT :limit OFFSET :offset'
        );
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':limit',   $limit,  PDO::PARAM_INT);
        $stmt->bindValue(':offset',  $offset, PDO::PARAM_INT);
        $stmt->execute();

        $orders = array_map(fn($row) => new Order($row), $stmt->fetchAll());
        foreach ($orders as $order) {
            $order->items = $this->getItems($order->id);
        }
        return $orders;
    }

    public function countByUser(int $userId): int
    {
        $stmt = $this->db->prepare('SELECT COUNT(*) FROM orders WHERE user_id = :user_id');
        $stmt->execute([':user_id' => $userId]);
        return (int)$stmt->fetchColumn();
    }

    public function getById(int $id): ?Order
    {
        $stmt = $this->db->prepare('SELECT * FROM orders WHERE id = :id');
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        if (!$row) return null;

        $order        = new Order($row);
        $order->items = $this->getItems($id);
        return $order;
    }

    public function create(Order $order, array $items): Order
    {
        $this->db->beginTransaction();
        try {
            $stmt = $this->db->prepare(
                'INSERT INTO orders (user_id, total, status) VALUES (:user_id, :total, :status)'
            );
            $stmt->execute([
                ':user_id' => $order->userId,
                ':total'   => $order->total,
                ':status'  => $order->status,
            ]);
            $order->id = (int)$this->db->lastInsertId();

            $itemStmt = $this->db->prepare('
                INSERT INTO order_items (order_id, product_id, quantity, price_at_purchase)
                VALUES (:order_id, :product_id, :quantity, :price_at_purchase)
            ');
            foreach ($items as $item) {
                $item->orderId = $order->id;
                $itemStmt->execute([
                    ':order_id'          => $order->id,
                    ':product_id'        => $item->productId,
                    ':quantity'          => $item->quantity,
                    ':price_at_purchase' => $item->priceAtPurchase,
                ]);
                $item->id = (int)$this->db->lastInsertId();
            }
            $order->items = $items;

            $this->db->commit();
            return $order;
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function updateStatus(int $id, string $status): bool
    {
        $stmt = $this->db->prepare('UPDATE orders SET status = :status WHERE id = :id');
        $stmt->execute([':status' => $status, ':id' => $id]);
        return $stmt->rowCount() > 0;
    }

    /** @return OrderItem[] */
    private function getItems(int $orderId): array
    {
        $stmt = $this->db->prepare('SELECT * FROM order_items WHERE order_id = :order_id');
        $stmt->execute([':order_id' => $orderId]);
        return array_map(fn($row) => new OrderItem($row), $stmt->fetchAll());
    }
}
