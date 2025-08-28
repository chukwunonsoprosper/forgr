<?php

require_once __DIR__ . '/vendor/autoload.php';

use Dotenv\Dotenv;
use Forgr\Core\App;
use Forgr\Core\Request;
use Forgr\Core\Response;
use Forgr\Middleware\CorsMiddleware;

// Load environment variables if .env exists
if (file_exists(__DIR__ . '/.env')) {
    $dotenv = Dotenv::createImmutable(__DIR__);
    $dotenv->load();
}

// Set up error handling
error_reporting(E_ALL);
ini_set('display_errors', 0);

try {
    // Apply CORS middleware
    $corsMiddleware = new CorsMiddleware();
    $corsMiddleware->handle();
    
    // Handle preflight requests
    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        $response = Response::success(['message' => 'CORS preflight OK']);
        $response->withCors()->send();
        exit;
    }

    // Initialize Forgr app
    $app = App::getInstance();

    // Load all routes
    $routeFiles = glob(__DIR__ . '/routes/**/*.php');
    foreach ($routeFiles as $routeFile) {
        require_once $routeFile;
    }

    // Get route name from request
    $request = new Request();
    $routeName = $request->getRouteName();

    if (!$routeName) {
        $response = Response::error('Route not specified. Invalid URI path.', 400);
        $response->withCors()->send();
        exit;
    }


    // Execute the route
    $response = $app->execute($routeName);

    // Send response with CORS headers
    $response->withCors()->send();
} catch (Exception $e) {
    error_log("Application error: " . $e->getMessage());
    $response = Response::error('Internal server error', 500);
    $response->withCors()->send();
} catch (Error $e) {
    error_log("Fatal error: " . $e->getMessage());
    $response = Response::error('Internal server error', 500);
    $response->withCors()->send();
}
