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
