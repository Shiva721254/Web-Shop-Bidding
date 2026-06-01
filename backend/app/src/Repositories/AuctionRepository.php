<?php

namespace App\Repositories;

use App\Models\Auction;
use App\Framework\Database;
use App\Repositories\Interfaces\IAuctionRepository;
use PDO;

class AuctionRepository implements IAuctionRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function closeExpired(): void
    {
        // Mark all open auctions whose end time has passed as closed
        $this->db->exec("
            UPDATE auctions
            SET status = 'closed'
            WHERE status = 'open' AND ends_at < NOW()
        ");

        // Set winner_id to the user who placed the highest bid
        $stmt = $this->db->query("
            SELECT a.id, b.user_id
            FROM auctions a
            JOIN bids b ON b.auction_id = a.id
            WHERE a.status = 'closed'
              AND a.winner_id IS NULL
              AND b.amount = (
                  SELECT MAX(b2.amount) FROM bids b2 WHERE b2.auction_id = a.id
              )
            GROUP BY a.id
        ");

        $update = $this->db->prepare(
            'UPDATE auctions SET winner_id = :winner WHERE id = :id'
        );
        foreach ($stmt->fetchAll() as $row) {
            $update->execute([':winner' => $row['user_id'], ':id' => $row['id']]);
        }
    }

    private const SELECT = '
        SELECT  a.id,
                a.product_id,
                p.title,
                p.description,
                p.category,
                p.seller_id,
                p.starting_price,
                a.current_bid,
                a.ends_at,
                a.status,
                a.winner_id
        FROM    auctions a
        JOIN    products p ON p.id = a.product_id
    ';

    /** @return Auction[] */
    public function getAll(array $filters = [], int $page = 1, int $limit = 10): array
    {
        $where  = [];
        $params = [];

        if (!empty($filters['status'])) {
            $where[]          = 'a.status = :status';
            $params[':status'] = $filters['status'];
        }
        if (!empty($filters['category'])) {
            $where[]            = 'p.category = :category';
            $params[':category'] = $filters['category'];
        }

        $sql    = self::SELECT;
        if ($where) {
            $sql .= ' WHERE ' . implode(' AND ', $where);
        }

        $offset       = ($page - 1) * $limit;
        $sql         .= ' ORDER BY a.ends_at ASC LIMIT :limit OFFSET :offset';
        $params[':limit']  = $limit;
        $params[':offset'] = $offset;

        $stmt = $this->db->prepare($sql);
        foreach ($params as $key => $value) {
            $type = is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR;
            $stmt->bindValue($key, $value, $type);
        }
        $stmt->execute();

        return array_map(fn($row) => new Auction($row), $stmt->fetchAll());
    }

    public function countAll(array $filters = []): int
    {
        $where  = [];
        $params = [];

        if (!empty($filters['status'])) {
            $where[]           = 'a.status = :status';
            $params[':status'] = $filters['status'];
        }
        if (!empty($filters['category'])) {
            $where[]             = 'p.category = :category';
            $params[':category'] = $filters['category'];
        }

        $sql = 'SELECT COUNT(*) FROM auctions a JOIN products p ON p.id = a.product_id';
        if ($where) {
            $sql .= ' WHERE ' . implode(' AND ', $where);
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int)$stmt->fetchColumn();
    }

    public function getById(int $id): ?Auction
    {
        $stmt = $this->db->prepare(self::SELECT . ' WHERE a.id = :id');
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ? new Auction($row) : null;
    }

    public function create(Auction $auction): Auction
    {
        $this->db->beginTransaction();
        try {
            $stmt = $this->db->prepare('
                INSERT INTO products (title, description, category, type, starting_price, seller_id)
                VALUES (:title, :description, :category, \'auction\', :starting_price, :seller_id)
            ');
            $stmt->execute([
                ':title'          => $auction->title,
                ':description'    => $auction->description,
                ':category'       => $auction->category,
                ':starting_price' => $auction->startingPrice,
                ':seller_id'      => $auction->sellerId,
            ]);
            $productId = (int)$this->db->lastInsertId();

            $stmt = $this->db->prepare('
                INSERT INTO auctions (product_id, current_bid, ends_at, status)
                VALUES (:product_id, :current_bid, :ends_at, :status)
            ');
            $stmt->execute([
                ':product_id'  => $productId,
                ':current_bid' => $auction->currentBid,
                ':ends_at'     => $auction->endsAt,
                ':status'      => $auction->status,
            ]);
            $auction->id        = (int)$this->db->lastInsertId();
            $auction->productId = $productId;

            $this->db->commit();
            return $auction;
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function update(Auction $auction): bool
    {
        $this->db->beginTransaction();
        try {
            $stmt = $this->db->prepare('
                UPDATE products
                SET title = :title, description = :description, category = :category,
                    starting_price = :starting_price
                WHERE id = (SELECT product_id FROM auctions WHERE id = :auction_id)
            ');
            $stmt->execute([
                ':title'          => $auction->title,
                ':description'    => $auction->description,
                ':category'       => $auction->category,
                ':starting_price' => $auction->startingPrice,
                ':auction_id'     => $auction->id,
            ]);

            $stmt = $this->db->prepare('
                UPDATE auctions
                SET current_bid = :current_bid, ends_at = :ends_at, status = :status
                WHERE id = :id
            ');
            $stmt->execute([
                ':current_bid' => $auction->currentBid,
                ':ends_at'     => $auction->endsAt,
                ':status'      => $auction->status,
                ':id'          => $auction->id,
            ]);

            $this->db->commit();
            return true;
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare('
            DELETE p FROM products p
            JOIN auctions a ON a.product_id = p.id
            WHERE a.id = :id
        ');
        $stmt->execute([':id' => $id]);
        return $stmt->rowCount() > 0;
    }
}
