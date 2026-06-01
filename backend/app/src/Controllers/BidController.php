<?php

namespace App\Controllers;

use App\Services\Interfaces\IBidService;
use App\Services\BidService;
use App\Framework\Controller;

class BidController extends Controller
{
    private IBidService $bidService;

    public function __construct()
    {
        $this->bidService = new BidService();
    }

    public function getByAuction($vars = [])
    {
        try {
            $auctionId = (int)($vars['auctionId'] ?? 0);
            $page      = max(1, (int)($_GET['page']  ?? 1));
            $limit     = min(100, max(1, (int)($_GET['limit'] ?? 10)));

            $result = $this->bidService->getByAuction($auctionId, $page, $limit);
            return $this->sendSuccessResponse($result);
        } catch (\Exception $e) {
            return $this->sendErrorResponse('Internal server error', 500);
        }
    }

    public function create($vars = [])
    {
        try {
            $auctionId = (int)($vars['auctionId'] ?? 0);
            $body      = $this->getJsonBody();

            $userId = (int)($body['userId'] ?? 0);
            $amount = isset($body['amount']) ? (float)$body['amount'] : null;

            if ($userId <= 0) {
                return $this->sendErrorResponse('userId is required', 422);
            }
            if ($amount === null || $amount <= 0) {
                return $this->sendErrorResponse('amount must be a positive number', 422);
            }

            $bid = $this->bidService->placeBid($auctionId, $userId, $amount);
            return $this->sendSuccessResponse($bid, 201);
        } catch (\InvalidArgumentException $e) {
            return $this->sendErrorResponse($e->getMessage(), $e->getCode() ?: 422);
        } catch (\Exception $e) {
            return $this->sendErrorResponse('Internal server error', 500);
        }
    }
}
