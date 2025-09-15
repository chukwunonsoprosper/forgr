<?php

/**
 * Forgr Helper Functions
 * These global functions make it super easy to register routes
 */

use Forgr\Core\App;

/**
 * Helper function to get the global Forgr app instance
 */
function forgr(): App
{
    return App::getInstance();
}

/**
 * Register a GET route
 */
function get(string $name): void
{
    App::getInstance()->register($name, ['method' => 'GET']);
}

/**
 * Register a POST route
 */
function post(string $name): void
{
    App::getInstance()->register($name, ['method' => 'POST']);
}

/**
 * Register a PUT route
 */
function put(string $name): void
{
    App::getInstance()->register($name, ['method' => 'PUT']);
}

/**
 * Register a DELETE route
 */
function delete(string $name): void
{
    App::getInstance()->register($name, ['method' => 'DELETE']);
}

/**
 * Register any route (default GET)
 */
function route(string $name, string $method = 'GET'): void
{
    App::getInstance()->register($name, ['method' => strtoupper($method)]);
}

/**
 * Include a PHP file relative to the project root
 * This helps solve path issues when including files from routes
 */
function forgr_include(string $relativePath): mixed
{
    $rootPath = realpath(__DIR__ . '/..');
    $fullPath = $rootPath . '/' . ltrim($relativePath, '/');
    
    if (!file_exists($fullPath)) {
        throw new \InvalidArgumentException("File not found: {$relativePath} (resolved to: {$fullPath})");
    }
    
    return include $fullPath;
}

/**
 * Require a PHP file relative to the project root
 * This helps solve path issues when requiring files from routes
 */
function forgr_require(string $relativePath): mixed
{
    $rootPath = realpath(__DIR__ . '/..');
    $fullPath = $rootPath . '/' . ltrim($relativePath, '/');
    
    if (!file_exists($fullPath)) {
        throw new \InvalidArgumentException("File not found: {$relativePath} (resolved to: {$fullPath})");
    }
    
    return require $fullPath;
}

/**
 * Require once a PHP file relative to the project root
 * This helps solve path issues when requiring files from routes
 */
function forgr_require_once(string $relativePath): mixed
{
    $rootPath = realpath(__DIR__ . '/..');
    $fullPath = $rootPath . '/' . ltrim($relativePath, '/');
    
    if (!file_exists($fullPath)) {
        throw new \InvalidArgumentException("File not found: {$relativePath} (resolved to: {$fullPath})");
    }
    
    return require_once $fullPath;
}

/**
 * Get the project root path
 */
function forgr_root(): string
{
    return realpath(__DIR__ . '/..');
}

/**
 * Get a path relative to the project root
 */
function forgr_path(string $relativePath): string
{
    return forgr_root() . '/' . ltrim($relativePath, '/');
}
