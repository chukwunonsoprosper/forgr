<?php

use Forgr\Core\Request;
use Forgr\Core\Response;

function user(Request $request): Response
{
    // Get URL parameters using $request->getQuery() for query parameters
    $name = $request->getQuery('name') ?? 'Guest';
    $age = $request->getQuery('age') ?? 'unknown';

    return Response::success([
        'message' => "Hello, {$name}!",
        'user' => [
            'name' => $name,
            'age' => $age,
        ],
    ]);
}
//register function as an API route

get('user');