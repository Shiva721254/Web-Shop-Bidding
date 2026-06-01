<?php

// NOTE: we are using a simplified file based storage for demo purposes
// For your assignment, you should use a database

namespace App\Repositories;

use App\Models\Auction;
use App\Utils\JsonStore;

class AuctionRepository implements IAuctionRepository
{
    private JsonStore $store;
    private const DATA_FILE = __DIR__ . '/../data/auctions.json';

    public function __construct()
    {
        $this->store = new JsonStore(self::DATA_FILE, Auction::class);
    }

    /**
     * @return Auction[]
     */
    public function getAll(): array
    {
        return $this->store->getAll();
    }

    public function getById(int $id): ?Auction
    {
        return $this->store->getById($id);
    }

    public function create(Auction $auction): Auction
    {
        $this->store->create($auction);
        return $auction;
    }

    public function update(Auction $auction): bool
    {
        return $this->store->update($auction);
    }

    public function delete(int $id): bool
    {
        return $this->store->delete($id);
    }
}