<?php

// Example of external utility functions
class ApiUtils {
    public static function validateEmail($email) {
        if (empty($email)) {
            throw new \InvalidArgumentException('Email cannot be empty');
        }
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException('Invalid email format');
        }
        
        return true;
    }
    
    public static function hashPassword($password) {
        if (strlen($password) < 6) {
            throw new \InvalidArgumentException('Password must be at least 6 characters');
        }
        
        return password_hash($password, PASSWORD_DEFAULT);
    }
    
    public static function generateApiKey() {
        return 'api_' . bin2hex(random_bytes(16));
    }
}

function logActivity($action, $user_id = null) {
    $timestamp = date('Y-m-d H:i:s');
    $log_entry = "[{$timestamp}] {$action}";
    if ($user_id) {
        $log_entry .= " (User: {$user_id})";
    }
    
    // In a real app, this would write to a log file or database
    error_log($log_entry);
    
    return $log_entry;
}