<?php

namespace App\Controllers;

use App\Models\Auction;
use App\Services\IAuctionService;
use App\Services\AuctionService;
use App\Framework\Controller;

class AuctionController extends Controller
{
    private IAuctionService $auctionService;

    public function __construct()
    {
        $this->auctionService = new AuctionService();
    }

    public function getAll()
    {
        try {
            $auctions = $this->auctionService->getAll();
            return $this->sendSuccessResponse($auctions);
        } catch (\Exception $e) {
            return $this->sendErrorResponse('Internal server error', 500);
        }
    }

    public function get($vars = [])
    {
        try {
            $id = (int)($vars['id'] ?? 0);
            $auction = $this->auctionService->getById($id);
            
            if (!$auction) {
                return $this->sendErrorResponse('Auction not found', 404);
            }
            return $this->sendSuccessResponse($auction);
        } catch (\Exception $e) {
            return $this->sendErrorResponse('Internal server error', 500);
        }
    }

    public function create()
    {
        try {
            $auction = $this->mapPostDataToClass(Auction::class);
            $auction = $this->auctionService->create($auction);
            return $this->sendSuccessResponse($auction, 201);
        } catch (\Exception $e) {
            return $this->sendErrorResponse('Internal server error', 500);
        }
    }

    public function update($vars = [])
    {
        try {
            $auction = $this->mapPostDataToClass(Auction::class);
            $id = (int)($vars['id'] ?? 0);
            $auction->id = $id;
            if (!$this->auctionService->update($auction)) {
                return $this->sendErrorResponse('Auction not found', 404);
            }
            return $this->sendSuccessResponse($auction);
        } catch (\Exception $e) {
            return $this->sendErrorResponse('Internal server error', 500);
        }
    }

    public function delete($vars = [])
    {
        try {
            $id = (int)($vars['id'] ?? 0);
            if (!$this->auctionService->delete($id)) {
                return $this->sendErrorResponse('Auction not found', 404);
            }
            return $this->sendSuccessResponse();
        } catch (\Exception $e) {
            return $this->sendErrorResponse('Internal server error', 500);
        }
    }


}
