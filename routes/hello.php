<?php

use Forgr\Core\Request;
use Forgr\Core\Response;

/**
 * Example hello world route
 * This is just an example - replace with your own functions
 */
function hello(Request $request): Response
{
    $body = $request->getBody();
    $name = $body['name'] ?? 'World';

    return Response::success([
        'message' => "Hello, {$name}!",
        'timestamp' => date('Y-m-d H:i:s')
    ]);
}

// Register the function as an API route
post('hello');
