<?php

namespace App\Repositories;

use App\Models\Product;
use App\Framework\Database;
use App\Repositories\Interfaces\IProductRepository;
use PDO;

class ProductRepository implements IProductRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    /** @return Product[] */
    public function getAll(array $filters = [], int $page = 1, int $limit = 10): array
    {
        $where  = [];
        $params = [];

        if (!empty($filters['type'])) {
            $where[]        = 'type = :type';
            $params[':type'] = $filters['type'];
        }
        if (!empty($filters['category'])) {
            $where[]            = 'category = :category';
            $params[':category'] = $filters['category'];
        }

        $sql = 'SELECT * FROM products';
        if ($where) {
            $sql .= ' WHERE ' . implode(' AND ', $where);
        }

        $offset           = ($page - 1) * $limit;
        $sql             .= ' ORDER BY created_at DESC LIMIT :limit OFFSET :offset';
        $params[':limit']  = $limit;
        $params[':offset'] = $offset;

        $stmt = $this->db->prepare($sql);
        foreach ($params as $key => $value) {
            $type = is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR;
            $stmt->bindValue($key, $value, $type);
        }
        $stmt->execute();

        return array_map(fn($row) => new Product($row), $stmt->fetchAll());
    }

    public function countAll(array $filters = []): int
    {
        $where  = [];
        $params = [];

        if (!empty($filters['type'])) {
            $where[]        = 'type = :type';
            $params[':type'] = $filters['type'];
        }
        if (!empty($filters['category'])) {
            $where[]            = 'category = :category';
            $params[':category'] = $filters['category'];
        }

        $sql = 'SELECT COUNT(*) FROM products';
        if ($where) {
            $sql .= ' WHERE ' . implode(' AND ', $where);
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int)$stmt->fetchColumn();
    }

    public function getById(int $id): ?Product
    {
        $stmt = $this->db->prepare('SELECT * FROM products WHERE id = :id');
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ? new Product($row) : null;
    }

    public function create(Product $product): Product
    {
        $this->db->beginTransaction();
        try {
            $stmt = $this->db->prepare('
                INSERT INTO products (title, description, category, type, price, starting_price, seller_id)
                VALUES (:title, :description, :category, :type, :price, :starting_price, :seller_id)
            ');
            $stmt->execute([
                ':title'          => $product->title,
                ':description'    => $product->description,
                ':category'       => $product->category,
                ':type'           => $product->type,
                ':price'          => $product->price,
                ':starting_price' => $product->startingPrice,
                ':seller_id'      => $product->sellerId,
            ]);
            $product->id = (int)$this->db->lastInsertId();

            if ($product->type === 'auction') {
                $stmt = $this->db->prepare('
                    INSERT INTO auctions (product_id, current_bid, ends_at, status)
                    VALUES (:product_id, NULL, :ends_at, \'open\')
                ');
                $stmt->execute([
                    ':product_id' => $product->id,
                    ':ends_at'    => $product->endsAt,
                ]);
            }

            $this->db->commit();
            return $product;
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function update(Product $product): bool
    {
        $stmt = $this->db->prepare('
            UPDATE products
            SET title = :title, description = :description, category = :category,
                type = :type, price = :price, starting_price = :starting_price
            WHERE id = :id
        ');
        $stmt->execute([
            ':title'          => $product->title,
            ':description'    => $product->description,
            ':category'       => $product->category,
            ':type'           => $product->type,
            ':price'          => $product->price,
            ':starting_price' => $product->startingPrice,
            ':id'             => $product->id,
        ]);
        return $stmt->rowCount() > 0;
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM products WHERE id = :id');
        $stmt->execute([':id' => $id]);
        return $stmt->rowCount() > 0;
    }
}
