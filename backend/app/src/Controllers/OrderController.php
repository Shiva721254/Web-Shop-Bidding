<?php

namespace App\Controllers;

use App\Services\IOrderService;
use App\Services\OrderService;
use App\Framework\Controller;

class OrderController extends Controller
{
    private IOrderService $orderService;

    public function __construct()
    {
        $this->orderService = new OrderService();
    }

    public function getAll()
    {
        try {
            $page   = max(1, (int)($_GET['page']  ?? 1));
            $limit  = min(100, max(1, (int)($_GET['limit'] ?? 10)));
            $result = $this->orderService->getAll($page, $limit);
            return $this->sendSuccessResponse($result);
        } catch (\Exception $e) {
            return $this->sendErrorResponse('Internal server error', 500);
        }
    }

    public function getByUser($vars = [])
    {
        try {
            $userId = (int)($vars['userId'] ?? 0);
            $page   = max(1, (int)($_GET['page']  ?? 1));
            $limit  = min(100, max(1, (int)($_GET['limit'] ?? 10)));
            $result = $this->orderService->getByUser($userId, $page, $limit);
            return $this->sendSuccessResponse($result);
        } catch (\Exception $e) {
            return $this->sendErrorResponse('Internal server error', 500);
        }
    }

    public function get($vars = [])
    {
        try {
            $id    = (int)($vars['id'] ?? 0);
            $order = $this->orderService->getById($id);
            if (!$order) {
                return $this->sendErrorResponse('Order not found', 404);
            }
            return $this->sendSuccessResponse($order);
        } catch (\Exception $e) {
            return $this->sendErrorResponse('Internal server error', 500);
        }
    }

    public function create()
    {
        try {
            $body   = $this->getJsonBody();
            $userId = isset($body['userId']) ? (int)$body['userId'] : 0;
            $items  = $body['items'] ?? [];

            if ($userId <= 0) {
                return $this->sendErrorResponse('userId is required', 422);
            }
            if (!is_array($items) || empty($items)) {
                return $this->sendErrorResponse('items array is required and must not be empty', 422);
            }

            $order = $this->orderService->createFromItems($userId, $items);
            return $this->sendSuccessResponse($order, 201);
        } catch (\InvalidArgumentException $e) {
            return $this->sendErrorResponse($e->getMessage(), $e->getCode() ?: 422);
        } catch (\Exception $e) {
            return $this->sendErrorResponse('Internal server error', 500);
        }
    }

    public function updateStatus($vars = [])
    {
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
        } catch (\Exception $e) {
            return $this->sendErrorResponse('Internal server error', 500);
        }
    }
}
