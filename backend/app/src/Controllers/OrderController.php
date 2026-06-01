<?php

namespace App\Controllers;

use App\Services\Interfaces\IOrderService;
use App\Services\OrderService;
use App\Framework\Controller;
use App\Framework\Auth;

class OrderController extends Controller
{
    private IOrderService $orderService;

    public function __construct()
    {
        $this->orderService = new OrderService();
    }

    public function getAll()
    {
        Auth::requireAuth();
        try {
            $page   = max(1, (int)($_GET['page']  ?? 1));
            $limit  = min(100, max(1, (int)($_GET['limit'] ?? 10)));
            $result = $this->orderService->getAll($page, $limit);
            return $this->sendSuccessResponse($result);
        } catch (\Exception) {
            return $this->sendErrorResponse('Internal server error', 500);
        }
    }

    public function getByUser($vars = [])
    {
        Auth::requireAuth();
        try {
            $userId = (int)($vars['userId'] ?? 0);
            $page   = max(1, (int)($_GET['page']  ?? 1));
            $limit  = min(100, max(1, (int)($_GET['limit'] ?? 10)));
            $result = $this->orderService->getByUser($userId, $page, $limit);
            return $this->sendSuccessResponse($result);
        } catch (\Exception) {
            return $this->sendErrorResponse('Internal server error', 500);
        }
    }

    public function get($vars = [])
    {
        Auth::requireAuth();
        try {
            $id    = (int)($vars['id'] ?? 0);
            $order = $this->orderService->getById($id);
            if (!$order) {
                return $this->sendErrorResponse('Order not found', 404);
            }
            return $this->sendSuccessResponse($order);
        } catch (\Exception) {
            return $this->sendErrorResponse('Internal server error', 500);
        }
    }

    public function create()
    {
        $authUser = Auth::requireAuth();
        try {
            $body  = $this->getJsonBody();
            $items = $body['items'] ?? [];

            if (!is_array($items) || empty($items)) {
                return $this->sendErrorResponse('items array is required and must not be empty', 422);
            }

            $order = $this->orderService->createFromItems($authUser->sub, $items);
            return $this->sendSuccessResponse($order, 201);
        } catch (\InvalidArgumentException $e) {
            return $this->sendErrorResponse($e->getMessage(), $e->getCode() ?: 422);
        } catch (\Exception) {
            return $this->sendErrorResponse('Internal server error', 500);
        }
    }

    public function updateStatus($vars = [])
    {
        Auth::requireAuth();
        try {
            $id   = (int)($vars['id'] ?? 0);
            $body = $this->getJsonBody();

            if (!$this->orderService->getById($id)) {
                return $this->sendErrorResponse('Order not found', 404);
            }

            $status = $body['status'] ?? '';
            if (empty($status)) {
                return $this->sendErrorResponse('status is required', 422);
            }

            if (!$this->orderService->updateStatus($id, $status)) {
                return $this->sendErrorResponse('Update failed', 500);
            }
            return $this->sendSuccessResponse(['id' => $id, 'status' => $status]);
        } catch (\InvalidArgumentException $e) {
            return $this->sendErrorResponse($e->getMessage(), $e->getCode() ?: 422);
        } catch (\Exception) {
            return $this->sendErrorResponse('Internal server error', 500);
        }
    }
}
