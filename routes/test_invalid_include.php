<?php

use Forgr\Core\Request;
use Forgr\Core\Response;

function test_invalid_include(Request $request): Response
{
    // This should trigger a detailed error about file not found
    forgr_require_once('nonexistent_file.php');
    
    return Response::success(['message' => 'This should not be reached']);
}

get('test_invalid_include');