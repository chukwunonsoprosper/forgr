<?php

namespace Forgr\Middleware;

class CorsMiddleware
{
    public function handle(): void
    {
        $origins = $_ENV['CORS_ORIGINS'] ?? '*';
        $methods = $_ENV['CORS_METHODS'] ?? 'GET,POST,PUT,DELETE,OPTIONS';
        $headers = $_ENV['CORS_HEADERS'] ?? 'Content-Type,Authorization,X-Route,X-Action,X-Admin-Key';
        
        // Handle specific origins or wildcard
        if ($origins === '*') {
            header('Access-Control-Allow-Origin: *');
        } else {
            $allowedOrigins = array_map('trim', explode(',', $origins));
            $origin = $_SERVER['HTTP_ORIGIN'] ?? '';
            
            if (in_array($origin, $allowedOrigins)) {
                header("Access-Control-Allow-Origin: {$origin}");
                header('Access-Control-Allow-Credentials: true');
            }
        }
        
        header("Access-Control-Allow-Methods: {$methods}");
        header("Access-Control-Allow-Headers: {$headers}");
        header('Access-Control-Max-Age: 86400'); // 24 hours
    }
}
