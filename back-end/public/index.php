<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;
use Core\Router;
use Core\Request;
use Core\Response;

// Load environment variables
$dotenv = Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

// Initialize JWT
Core\JWT::init();

// Enable CORS
header('Access-Control-Allow-Origin: ' . $_ENV['CORS_ALLOWED_ORIGINS']);
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Initialize router
$router = new Router();

// Define routes
require_once __DIR__ . '/../routes/api.php';

// Handle request
$request = new Request();
$response = new Response();

try {
    $router->dispatch($request, $response);
} catch (\Exception $e) {
    $response->json([
        'error' => true,
        'message' => $e->getMessage()
    ], 500);
}