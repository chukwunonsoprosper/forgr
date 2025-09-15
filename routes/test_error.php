<?php

use Forgr\Core\Request;
use Forgr\Core\Response;

function test_error(Request $request): Response
{
    // This will cause an error - calling undefined function
    $result = undefined_function_call();
    
    return Response::success(['result' => $result]);
}

get('test_error');