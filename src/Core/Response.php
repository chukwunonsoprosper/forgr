<?php

namespace Forgr\Core;

/**
 * HTTP Response builder for Forgr API routes
 */
class Response
{
    private int $statusCode = 200;
    private array $headers = [];
    private mixed $data = null;
    private string $contentType = 'application/json';
    
    public function __construct(mixed $data = null, int $statusCode = 200, array $headers = [])
    {
        $this->data = $data;
        $this->statusCode = $statusCode;
        $this->headers = $headers;
    }
    
    /**
     * Create JSON response
     */
    public static function json(mixed $data, int $statusCode = 200, array $headers = []): self
    {
        return new self($data, $statusCode, array_merge($headers, [
            'Content-Type' => 'application/json'
        ]));
    }
    
    /**
     * Create success response
     */
    public static function success(mixed $data = null, string $message = 'Success'): self
    {
        return self::json([
            'success' => true,
            'message' => $message,
            'data' => $data,
            'timestamp' => date('Y-m-d H:i:s')
        ]);
    }
    
    /**
     * Create error response
     */
    public static function error(string $message, int $statusCode = 400, array $details = []): self
    {
        $data = [
            'success' => false,
            'error' => $message,
            'timestamp' => date('Y-m-d H:i:s')
        ];
        
        if (!empty($details)) {
            $data['details'] = $details;
        }
        
        return self::json($data, $statusCode);
    }
    
    /**
     * Create unauthorized response
     */
    public static function unauthorized(string $message = 'Unauthorized'): self
    {
        return self::error($message, 401);
    }
    
    /**
     * Create forbidden response
     */
    public static function forbidden(string $message = 'Forbidden'): self
    {
        return self::error($message, 403);
    }
    
    /**
     * Create not found response
     */
    public static function notFound(string $message = 'Not found'): self
    {
        return self::error($message, 404);
    }
    
    /**
     * Create method not allowed response
     */
    public static function methodNotAllowed(string $message = 'Method not allowed'): self
    {
        return self::error($message, 405);
    }
    
    /**
     * Create created response
     */
    public static function created(mixed $data = null, string $message = 'Created successfully'): self
    {
        return self::success($data, $message)->setStatusCode(201);
    }
    
    /**
     * Create HTML response
     */
    public static function html(string $content, int $statusCode = 200, array $headers = []): self
    {
        $response = new self($content, $statusCode, array_merge($headers, [
            'Content-Type' => 'text/html'
        ]));
        $response->contentType = 'text/html';
        return $response;
    }
    
    /**
     * Create plain text response
     */
    public static function text(string $content, int $statusCode = 200, array $headers = []): self
    {
        $response = new self($content, $statusCode, array_merge($headers, [
            'Content-Type' => 'text/plain'
        ]));
        $response->contentType = 'text/plain';
        return $response;
    }
    
    /**
     * Create redirect response
     */
    public static function redirect(string $url, int $statusCode = 302): self
    {
        return new self(null, $statusCode, [
            'Location' => $url
        ]);
    }
    
    /**
     * Set status code
     */
    public function setStatusCode(int $statusCode): self
    {
        $this->statusCode = $statusCode;
        return $this;
    }
    
    /**
     * Get status code
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }
    
    /**
     * Set header
     */
    public function setHeader(string $name, string $value): self
    {
        $this->headers[$name] = $value;
        return $this;
    }
    
    /**
     * Get header
     */
    public function getHeader(string $name): ?string
    {
        return $this->headers[$name] ?? null;
    }
    
    /**
     * Get all headers
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }
    
    /**
     * Get response data
     */
    public function getData(): mixed
    {
        return $this->data;
    }
    
    /**
     * Set response data
     */
    public function setData(mixed $data): self
    {
        $this->data = $data;
        return $this;
    }
    
    /**
     * Send the response
     */
    public function send(): void
    {
        // Set status code
        http_response_code($this->statusCode);
        
        // Set headers
        foreach ($this->headers as $name => $value) {
            header("{$name}: {$value}");
        }
        
        // Output content
        if ($this->data !== null) {
            if ($this->contentType === 'application/json') {
                echo json_encode($this->data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
            } else {
                echo $this->data;
            }
        }
    }
    
    /**
     * Convert response to JSON string
     */
    public function toJson(): string
    {
        return json_encode($this->data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }
    
    /**
     * Convert response to array
     */
    public function toArray(): array
    {
        return is_array($this->data) ? $this->data : ['data' => $this->data];
    }
    
    /**
     * Add CORS headers
     */
    public function withCors(array $origins = ['*'], array $methods = ['GET', 'POST', 'PUT', 'DELETE', 'OPTIONS']): self
    {
        $origin = $_SERVER['HTTP_ORIGIN'] ?? '';
        
        if (in_array('*', $origins) || in_array($origin, $origins)) {
            $this->setHeader('Access-Control-Allow-Origin', in_array('*', $origins) ? '*' : $origin);
        }
        
        $this->setHeader('Access-Control-Allow-Methods', implode(', ', $methods));
        $this->setHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Route, X-Requested-With');
        $this->setHeader('Access-Control-Max-Age', '86400');
        
        return $this;
    }
}
