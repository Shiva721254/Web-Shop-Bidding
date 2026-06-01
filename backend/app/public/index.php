<?php

use FastRoute\Dispatcher;
use FastRoute\RouteCollector;
use function FastRoute\simpleDispatcher;

// CORS headers for localhost requests
$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
if (preg_match('/^https?:\/\/(localhost|127\.0\.0\.1|::1)(:\d+)?$/', $origin)) {
    header('Access-Control-Allow-Origin: ' . $origin);
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
    header('Access-Control-Allow-Credentials: true');
    header('Access-Control-Max-Age: 86400');
}

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require __DIR__ . '/../vendor/autoload.php';

$dispatcher = simpleDispatcher(function (RouteCollector $r) {
    // Auth routes (public)
    $r->addRoute('POST', '/auth/register', ['App\Controllers\AuthController', 'register']);
    $r->addRoute('POST', '/auth/login',    ['App\Controllers\AuthController', 'login']);

    // Product routes
    $r->addRoute('GET',    '/products',      ['App\Controllers\ProductController', 'getAll']);
    $r->addRoute('GET',    '/products/{id}', ['App\Controllers\ProductController', 'get']);
    $r->addRoute('POST',   '/products',      ['App\Controllers\ProductController', 'create']);
    $r->addRoute('PUT',    '/products/{id}', ['App\Controllers\ProductController', 'update']);
    $r->addRoute('DELETE', '/products/{id}', ['App\Controllers\ProductController', 'delete']);

    // Auction routes
    $r->addRoute('GET',    '/auctions',      ['App\Controllers\AuctionController', 'getAll']);
    $r->addRoute('GET',    '/auctions/{id}', ['App\Controllers\AuctionController', 'get']);
    $r->addRoute('POST',   '/auctions',      ['App\Controllers\AuctionController', 'create']);
    $r->addRoute('PUT',    '/auctions/{id}', ['App\Controllers\AuctionController', 'update']);
    $r->addRoute('DELETE', '/auctions/{id}', ['App\Controllers\AuctionController', 'delete']);

    // Bid routes (nested under auctions)
    $r->addRoute('GET',  '/auctions/{auctionId}/bids', ['App\Controllers\BidController', 'getByAuction']);
    $r->addRoute('POST', '/auctions/{auctionId}/bids', ['App\Controllers\BidController', 'create']);

    // Order routes
    $r->addRoute('GET',  '/orders',               ['App\Controllers\OrderController', 'getAll']);
    $r->addRoute('GET',  '/orders/{id}',           ['App\Controllers\OrderController', 'get']);
    $r->addRoute('GET',  '/users/{userId}/orders', ['App\Controllers\OrderController', 'getByUser']);
    $r->addRoute('POST', '/orders',               ['App\Controllers\OrderController', 'create']);
    $r->addRoute('PUT',  '/orders/{id}/status',   ['App\Controllers\OrderController', 'updateStatus']);
});

$httpMethod = $_SERVER['REQUEST_METHOD'];
$uri        = strtok($_SERVER['REQUEST_URI'], '?');
$routeInfo  = $dispatcher->dispatch($httpMethod, $uri);

switch ($routeInfo[0]) {
    case Dispatcher::NOT_FOUND:
        http_response_code(404);
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Not Found']);
        break;

    case Dispatcher::METHOD_NOT_ALLOWED:
        http_response_code(405);
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Method Not Allowed']);
        break;

    case Dispatcher::FOUND:
        [$class, $method] = $routeInfo[1];
        $vars             = $routeInfo[2];
        $controller       = new $class();
        $controller->$method($vars);
        break;
}
