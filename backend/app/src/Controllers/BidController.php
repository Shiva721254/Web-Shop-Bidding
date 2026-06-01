<?php

namespace App\Controllers;

use App\Services\Interfaces\IBidService;
use App\Services\BidService;
use App\Framework\Controller;
use App\Framework\Auth;

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
        } catch (\Exception) {
            return $this->sendErrorResponse('Internal server error', 500);
        }
    }

    public function create($vars = [])
    {
        $authUser  = Auth::requireAuth();
        try {
            $auctionId = (int)($vars['auctionId'] ?? 0);
            $body      = $this->getJsonBody();
            $amount    = isset($body['amount']) ? (float)$body['amount'] : null;

            if ($amount === null || $amount <= 0) {
                return $this->sendErrorResponse('amount must be a positive number', 422);
            }

            $bid = $this->bidService->placeBid($auctionId, $authUser->sub, $amount);
            return $this->sendSuccessResponse($bid, 201);
        } catch (\InvalidArgumentException $e) {
            return $this->sendErrorResponse($e->getMessage(), $e->getCode() ?: 422);
        } catch (\Exception) {
            return $this->sendErrorResponse('Internal server error', 500);
        }
    }
}
