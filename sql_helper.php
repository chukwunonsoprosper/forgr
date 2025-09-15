<?php

// Sample database connection helper
function getDbConnection() {
    return "Database connection established";
}

function sql_query($query) {
    return ["result" => "Query executed: " . $query];
}

$db_config = [
    'host' => 'localhost',
    'database' => 'testdb',
    'username' => 'user',
    'password' => 'pass'
];