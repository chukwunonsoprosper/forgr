<?php

use Forgr\Core\Request;
use Forgr\Core\Response;

/**
 * Simple GET example
 */
function status(Request $request): Response
{
    return Response::success([
        'status' => 'OK',
        'time' => date('Y-m-d H:i:s'),
        'message' => 'Forgr is running!'
    ]);
}

// Super simple registration - just one line!
get('status');
