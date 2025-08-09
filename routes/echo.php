<?php

use Forgr\Core\Request;
use Forgr\Core\Response;

/**
 * Echo back what user sends
 */
function echo_data(Request $request): Response
{
    $data = $request->getBody();
    
    return Response::success([
        'echo' => $data,
        'method' => $request->getMethod(),
        'received_at' => date('Y-m-d H:i:s')
    ]);
}

// Register as POST route
post('echo_data');
