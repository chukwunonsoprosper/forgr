<?php 
use Forgr\Core\Request;
use Forgr\Core\Response;

function user(Request $request): Response
{
    return Response::success([
        'message' => "Hello, World!",
    ]);
}

// Register the function as an API route
post('user');