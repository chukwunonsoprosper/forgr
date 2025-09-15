<?php

// Sample database connection helper
class DatabaseConnection {
    private $config;
    
    public function __construct($config) {
        $this->config = $config;
    }
    
    public function connect() {
        return "Database connection established to " . $this->config['database'] . " at " . $this->config['host'];
    }
    
    public function query($sql) {
        return [
            'sql' => $sql,
            'result' => 'Query executed successfully',
            'rows' => rand(1, 100),
            'time' => round(microtime(true) * 1000, 2) . 'ms'
        ];
    }
}

function getDbConnection() {
    $db_config = $GLOBALS['db_config'];
    $db = new DatabaseConnection($db_config);
    return $db->connect();
}

function sql_query($query) {
    $db_config = $GLOBALS['db_config'];
    $db = new DatabaseConnection($db_config);
    return $db->query($query);
}

$GLOBALS['db_config'] = [
    'host' => 'localhost',
    'database' => 'testdb',
    'username' => 'user',
    'password' => 'pass'
];