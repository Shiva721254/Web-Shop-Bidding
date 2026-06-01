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
            $filters = [];
            if (!empty($_GET['status']))   $filters['status']   = $_GET['status'];
            if (!empty($_GET['category'])) $filters['category'] = $_GET['category'];

            $page  = max(1, (int)($_GET['page']  ?? 1));
            $limit = min(100, max(1, (int)($_GET['limit'] ?? 10)));

            $result = $this->auctionService->getAll($filters, $page, $limit);
            return $this->sendSuccessResponse($result);
        } catch (\Exception $e) {
            return $this->sendErrorResponse('Internal server error', 500);
        }
    }

    public function get($vars = [])
    {
        try {
            $id      = (int)($vars['id'] ?? 0);
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

            if (empty($auction->title)) {
                return $this->sendErrorResponse('Title is required', 422);
            }
            if ($auction->startingPrice <= 0) {
                return $this->sendErrorResponse('Starting price must be greater than zero', 422);
            }
            if (empty($auction->endsAt)) {
                return $this->sendErrorResponse('End date is required', 422);
            }

            $auction = $this->auctionService->create($auction);
            return $this->sendSuccessResponse($auction, 201);
        } catch (\InvalidArgumentException $e) {
            return $this->sendErrorResponse($e->getMessage(), 400);
        } catch (\Exception $e) {
            return $this->sendErrorResponse('Internal server error', 500);
        }
    }

    public function update($vars = [])
    {
        try {
            $id      = (int)($vars['id'] ?? 0);
            $auction = $this->mapPostDataToClass(Auction::class);
            $auction->id = $id;

            if (!$this->auctionService->getById($id)) {
                return $this->sendErrorResponse('Auction not found', 404);
            }
            if (!$this->auctionService->update($auction)) {
                return $this->sendErrorResponse('Update failed', 500);
            }
            return $this->sendSuccessResponse($auction);
        } catch (\InvalidArgumentException $e) {
            return $this->sendErrorResponse($e->getMessage(), 400);
        } catch (\Exception $e) {
            return $this->sendErrorResponse('Internal server error', 500);
        }
    }

    public function delete($vars = [])
    {
        try {
            $id = (int)($vars['id'] ?? 0);
            if (!$this->auctionService->getById($id)) {
                return $this->sendErrorResponse('Auction not found', 404);
            }
            if (!$this->auctionService->delete($id)) {
                return $this->sendErrorResponse('Delete failed', 500);
            }
            return $this->sendSuccessResponse(null, 204);
        } catch (\Exception $e) {
            return $this->sendErrorResponse('Internal server error', 500);
        }
    }
}
