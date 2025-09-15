<?php

use Forgr\Core\Request;
use Forgr\Core\Response;

// Use the new helper function to include external PHP file
forgr_require_once('sql_helper.php');

function test_include(Request $request): Response
{
    $connection = getDbConnection();
    $query_result = sql_query("SELECT * FROM users WHERE active = 1");
    
    return Response::success([
        'connection_status' => $connection,
        'query_result' => $query_result,
        'config' => $GLOBALS['db_config'],
        'timestamp' => date('Y-m-d H:i:s'),
        'message' => 'Successfully included external SQL helper and executed operations'
    ]);
}

get('test_include');