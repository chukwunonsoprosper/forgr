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

function resolveRoutePath(string $path): string
{
    // Remove query params if present
    $path = parse_url($path, PHP_URL_PATH) ?? '';

    // Trim slashes
    $path = '/' . trim($path, '/');

    // Special case for root
    return $path === '/' ? '/' : $path;
}


/**
 * Register a GET route
 */

function get(string $uri, string $functionName): void
{
    $uri = resolveRoutePath($uri);

    $config['method'] = 'GET';
    App::getInstance()->register($uri, $functionName, $config);
}

/**
 * Register a POST route
 */
function post(string $uri, string $functionName): void
{
    $uri = resolveRoutePath($uri);

    $config['method'] = 'POST';
    App::getInstance()->register($uri, $functionName, $config);
}


/**
 * Register a PUT route
 */
function put(string $uri, string $functionName): void
{
    $uri = resolveRoutePath($uri);

    $config['method'] = 'PUT';
    App::getInstance()->register($uri, $functionName, $config);
}


/**
 * Register a DELETE route
 */
function delete(string $uri, string $functionName): void
{
    $uri = resolveRoutePath($uri);

    $config['method'] = 'DELETE';
    App::getInstance()->register($uri, $functionName, $config);
}

/**
 * Register any route (default GET)
*/
function route(string $uri, string $functionName, string $method = 'GET'): void
{
    $uri = resolveRoutePath($uri);

    $config['method'] = strtoupper($method);
    App::getInstance()->register($uri, $functionName, $config);
}