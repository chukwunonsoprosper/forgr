<?php

use Forgr\Core\Request;
use Forgr\Core\Response;

// Try to include external PHP file
require_once '../sql_helper.php';

function test_include(Request $request): Response
{
    $connection = getDbConnection();
    $query_result = sql_query("SELECT * FROM users");
    
    return Response::success([
        'connection' => $connection,
        'query' => $query_result,
        'config' => $db_config ?? 'Config not found'
    ]);
}

get('test_include');