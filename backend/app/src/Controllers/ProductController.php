<?php

namespace App\Controllers;

use App\Models\Product;
use App\Services\Interfaces\IProductService;
use App\Services\ProductService;
use App\Framework\Controller;
use App\Framework\Auth;

class ProductController extends Controller
{
    private IProductService $productService;

    public function __construct()
    {
        $this->productService = new ProductService();
    }

    public function getAll()
    {
        try {
            $filters = [];
            if (!empty($_GET['type']))     $filters['type']     = $_GET['type'];
            if (!empty($_GET['category'])) $filters['category'] = $_GET['category'];

            $page  = max(1, (int)($_GET['page']  ?? 1));
            $limit = min(100, max(1, (int)($_GET['limit'] ?? 10)));

            $result = $this->productService->getAll($filters, $page, $limit);
            return $this->sendSuccessResponse($result);
        } catch (\Exception) {
            return $this->sendErrorResponse('Internal server error', 500);
        }
    }

    public function get($vars = [])
    {
        try {
            $id      = (int)($vars['id'] ?? 0);
            $product = $this->productService->getById($id);

            if (!$product) {
                return $this->sendErrorResponse('Product not found', 404);
            }
            return $this->sendSuccessResponse($product);
        } catch (\Exception) {
            return $this->sendErrorResponse('Internal server error', 500);
        }
    }

    public function create()
    {
        Auth::requireAuth();
        try {
            $product = $this->mapPostDataToClass(Product::class);

            if (empty($product->title)) {
                return $this->sendErrorResponse('Title is required', 422);
            }
            if (!in_array($product->type, ['buy_now', 'auction'], true)) {
                return $this->sendErrorResponse('Type must be buy_now or auction', 422);
            }
            if ($product->type === 'buy_now' && ($product->price === null || $product->price <= 0)) {
                return $this->sendErrorResponse('Price is required for buy_now products', 422);
            }
            if ($product->type === 'auction' && ($product->startingPrice === null || $product->startingPrice <= 0)) {
                return $this->sendErrorResponse('Starting price is required for auction products', 422);
            }

            $product = $this->productService->create($product);
            return $this->sendSuccessResponse($product, 201);
        } catch (\InvalidArgumentException $e) {
            return $this->sendErrorResponse($e->getMessage(), 400);
        } catch (\Exception) {
            return $this->sendErrorResponse('Internal server error', 500);
        }
    }

    public function update($vars = [])
    {
        Auth::requireAuth();
        try {
            $id      = (int)($vars['id'] ?? 0);
            $product = $this->productService->getById($id);

            if (!$product) {
                return $this->sendErrorResponse('Product not found', 404);
            }

            $updated     = $this->mapPostDataToClass(Product::class);
            $updated->id = $id;

            if (empty($updated->title)) {
                return $this->sendErrorResponse('Title is required', 422);
            }

            if (!$this->productService->update($updated)) {
                return $this->sendErrorResponse('Update failed', 500);
            }
            return $this->sendSuccessResponse($updated);
        } catch (\InvalidArgumentException $e) {
            return $this->sendErrorResponse($e->getMessage(), 400);
        } catch (\Exception) {
            return $this->sendErrorResponse('Internal server error', 500);
        }
    }

    public function delete($vars = [])
    {
        Auth::requireAuth();
        try {
            $id      = (int)($vars['id'] ?? 0);
            $product = $this->productService->getById($id);

            if (!$product) {
                return $this->sendErrorResponse('Product not found', 404);
            }
            if (!$this->productService->delete($id)) {
                return $this->sendErrorResponse('Delete failed', 500);
            }
            return $this->sendSuccessResponse(null, 204);
        } catch (\Exception) {
            return $this->sendErrorResponse('Internal server error', 500);
        }
    }
}
