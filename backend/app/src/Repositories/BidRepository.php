<?php

namespace App\Repositories;

use App\Models\Bid;
use App\Framework\Database;
use App\Repositories\Interfaces\IBidRepository;
use PDO;

class BidRepository implements IBidRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    /** @return Bid[] */
    public function getByAuction(int $auctionId, int $page = 1, int $limit = 10): array
    {
        $offset = ($page - 1) * $limit;
        $stmt   = $this->db->prepare('
            SELECT * FROM bids
            WHERE auction_id = :auction_id
            ORDER BY amount DESC, created_at DESC
            LIMIT :limit OFFSET :offset
        ');
        $stmt->bindValue(':auction_id', $auctionId, PDO::PARAM_INT);
        $stmt->bindValue(':limit',      $limit,     PDO::PARAM_INT);
        $stmt->bindValue(':offset',     $offset,    PDO::PARAM_INT);
        $stmt->execute();

        return array_map(fn($row) => new Bid($row), $stmt->fetchAll());
    }

    public function countByAuction(int $auctionId): int
    {
        $stmt = $this->db->prepare('SELECT COUNT(*) FROM bids WHERE auction_id = :auction_id');
        $stmt->execute([':auction_id' => $auctionId]);
        return (int)$stmt->fetchColumn();
    }

    public function getById(int $id): ?Bid
    {
        $stmt = $this->db->prepare('SELECT * FROM bids WHERE id = :id');
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ? new Bid($row) : null;
    }

    public function create(Bid $bid): Bid
    {
        $stmt = $this->db->prepare('
            INSERT INTO bids (auction_id, user_id, amount)
            VALUES (:auction_id, :user_id, :amount)
        ');
        $stmt->execute([
            ':auction_id' => $bid->auctionId,
            ':user_id'    => $bid->userId,
            ':amount'     => $bid->amount,
        ]);
        $bid->id = (int)$this->db->lastInsertId();
        return $bid;
    }

    public function updateAuctionCurrentBid(int $auctionId, float $amount): void
    {
        $stmt = $this->db->prepare('
            UPDATE auctions SET current_bid = :amount WHERE id = :id
        ');
        $stmt->execute([':amount' => $amount, ':id' => $auctionId]);
    }
}
