<?php

namespace App\Services;

use App\Models\Bid;
use App\Repositories\IBidRepository;
use App\Repositories\BidRepository;
use App\Repositories\IAuctionRepository;
use App\Repositories\AuctionRepository;

class BidService implements IBidService
{
    private IBidRepository     $bidRepository;
    private IAuctionRepository $auctionRepository;

    public function __construct()
    {
        $this->bidRepository     = new BidRepository();
        $this->auctionRepository = new AuctionRepository();
    }

    public function getByAuction(int $auctionId, int $page = 1, int $limit = 10): array
    {
        return [
            'data'  => $this->bidRepository->getByAuction($auctionId, $page, $limit),
            'total' => $this->bidRepository->countByAuction($auctionId),
            'page'  => $page,
            'limit' => $limit,
        ];
    }

    public function placeBid(int $auctionId, int $userId, float $amount): Bid
    {
        $auction = $this->auctionRepository->getById($auctionId);

        if (!$auction) {
            throw new \InvalidArgumentException('Auction not found', 404);
        }
        if ($auction->status !== 'open') {
            throw new \InvalidArgumentException('Auction is closed', 409);
        }
        if (new \DateTime() > new \DateTime($auction->endsAt)) {
            throw new \InvalidArgumentException('Auction has ended', 409);
        }

        $minimumBid = $auction->currentBid ?? $auction->startingPrice;
        if ($amount <= $minimumBid) {
            throw new \InvalidArgumentException(
                "Bid must be higher than the current bid of {$minimumBid}", 422
            );
        }

        $bid            = new Bid();
        $bid->auctionId = $auctionId;
        $bid->userId    = $userId;
        $bid->amount    = $amount;

        $bid = $this->bidRepository->create($bid);
        $this->bidRepository->updateAuctionCurrentBid($auctionId, $amount);

        return $bid;
    }
}
