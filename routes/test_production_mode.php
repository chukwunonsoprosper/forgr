<?php

use Forgr\Core\Request;
use Forgr\Core\Response;

function test_production_mode(Request $request): Response
{
    // Temporarily disable debug mode
    $_ENV['FORGR_DEBUG'] = false;
    
    // Cause an error
    $undefinedVariable->someMethod();
    
    return Response::success(['message' => 'This should not be reached']);
}

get('test_production_mode');