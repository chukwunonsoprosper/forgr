<?php

// Complete example demonstrating Forgr's enhanced features

use Forgr\Core\Request;
use Forgr\Core\Response;

// Include external helper files using the new helper functions
forgr_require_once('sql_helper.php');
forgr_require_once('utils.php');

function register_user(Request $request): Response
{
    // Get request data
    $data = $request->getBody();
    $email = $data['email'] ?? '';
    $password = $data['password'] ?? '';
    $name = $data['name'] ?? '';
    
    try {
        // Validate input using external utility class
        ApiUtils::validateEmail($email);
        $hashedPassword = ApiUtils::hashPassword($password);
        $apiKey = ApiUtils::generateApiKey();
        
        // Log the activity
        logActivity("User registration attempt for email: {$email}");
        
        // Simulate database operations
        $connection = getDbConnection();
        $insertQuery = sql_query("INSERT INTO users (name, email, password, api_key) VALUES ('{$name}', '{$email}', '{$hashedPassword}', '{$apiKey}')");
        
        // Successful response
        $result = [
            'user' => [
                'name' => $name,
                'email' => $email,
                'api_key' => $apiKey,
                'registered_at' => date('Y-m-d H:i:s')
            ],
            'database' => [
                'connection' => $connection,
                'insert_result' => $insertQuery
            ]
        ];
        
        logActivity("User registered successfully", $email);
        
        return Response::created($result, 'User registered successfully');
        
    } catch (\InvalidArgumentException $e) {
        // Custom validation errors
        return Response::error($e->getMessage(), 400, [
            'field_errors' => true,
            'provided_data' => array_keys($data)
        ]);
    }
}

post('register_user');