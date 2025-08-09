<?php

namespace Forgr\HTTP;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\RequestException;
use Exception;

/**
 * HTTP client for external API calls in Forgr routes
 */
class Client
{
    private GuzzleClient $client;
    private array $defaultOptions;
    
    public function __construct(array $options = [])
    {
        $this->defaultOptions = array_merge([
            'timeout' => 30,
            'connect_timeout' => 10,
            'http_errors' => false, // Don't throw exceptions on HTTP errors
            'verify' => true, // Verify SSL certificates
        ], $options);
        
        $this->client = new GuzzleClient($this->defaultOptions);
    }
    
    /**
     * Make a GET request
     */
    public function get(string $url, array $options = []): HttpResponse
    {
        return $this->request('GET', $url, $options);
    }
    
    /**
     * Make a POST request
     */
    public function post(string $url, array $data = [], array $options = []): HttpResponse
    {
        if (!empty($data)) {
            $options['json'] = $data;
        }
        
        return $this->request('POST', $url, $options);
    }
    
    /**
     * Make a PUT request
     */
    public function put(string $url, array $data = [], array $options = []): HttpResponse
    {
        if (!empty($data)) {
            $options['json'] = $data;
        }
        
        return $this->request('PUT', $url, $options);
    }
    
    /**
     * Make a PATCH request
     */
    public function patch(string $url, array $data = [], array $options = []): HttpResponse
    {
        if (!empty($data)) {
            $options['json'] = $data;
        }
        
        return $this->request('PATCH', $url, $options);
    }
    
    /**
     * Make a DELETE request
     */
    public function delete(string $url, array $options = []): HttpResponse
    {
        return $this->request('DELETE', $url, $options);
    }
    
    /**
     * Make a custom HTTP request
     */
    public function request(string $method, string $url, array $options = []): HttpResponse
    {
        // Security: Validate URL
        if (!$this->isUrlAllowed($url)) {
            throw new Exception('URL not allowed: ' . $url);
        }
        
        try {
            $response = $this->client->request($method, $url, $options);
            
            return new HttpResponse(
                $response->getStatusCode(),
                $response->getHeaders(),
                $response->getBody()->getContents()
            );
            
        } catch (RequestException $e) {
            $statusCode = $e->getResponse() ? $e->getResponse()->getStatusCode() : 0;
            $body = $e->getResponse() ? $e->getResponse()->getBody()->getContents() : '';
            $headers = $e->getResponse() ? $e->getResponse()->getHeaders() : [];
            
            return new HttpResponse($statusCode, $headers, $body, $e->getMessage());
        } catch (Exception $e) {
            throw new Exception('HTTP request failed: ' . $e->getMessage());
        }
    }
    
    /**
     * Set default headers for all requests
     */
    public function setDefaultHeaders(array $headers): self
    {
        $this->defaultOptions['headers'] = array_merge(
            $this->defaultOptions['headers'] ?? [],
            $headers
        );
        
        // Recreate client with new options
        $this->client = new GuzzleClient($this->defaultOptions);
        
        return $this;
    }
    
    /**
     * Set authorization header
     */
    public function withAuth(string $token, string $type = 'Bearer'): self
    {
        return $this->setDefaultHeaders([
            'Authorization' => "{$type} {$token}"
        ]);
    }
    
    /**
     * Set user agent
     */
    public function withUserAgent(string $userAgent): self
    {
        return $this->setDefaultHeaders([
            'User-Agent' => $userAgent
        ]);
    }
    
    private function isUrlAllowed(string $url): bool
    {
        $parsed = parse_url($url);
        
        if (!$parsed || !isset($parsed['scheme'], $parsed['host'])) {
            return false;
        }
        
        // Only allow HTTP and HTTPS
        if (!in_array($parsed['scheme'], ['http', 'https'])) {
            return false;
        }
        
        // Block private IP ranges
        $ip = gethostbyname($parsed['host']);
        if ($this->isPrivateIP($ip)) {
            return false;
        }
        
        return true;
    }
    
    private function isPrivateIP(string $ip): bool
    {
        return !filter_var(
            $ip,
            FILTER_VALIDATE_IP,
            FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE
        );
    }
}

/**
 * HTTP response wrapper
 */
class HttpResponse
{
    private int $statusCode;
    private array $headers;
    private string $body;
    private ?string $error;
    
    public function __construct(int $statusCode, array $headers, string $body, ?string $error = null)
    {
        $this->statusCode = $statusCode;
        $this->headers = $headers;
        $this->body = $body;
        $this->error = $error;
    }
    
    /**
     * Get HTTP status code
     */
    public function status(): int
    {
        return $this->statusCode;
    }
    
    /**
     * Get response headers
     */
    public function headers(): array
    {
        return $this->headers;
    }
    
    /**
     * Get specific header
     */
    public function header(string $name): ?string
    {
        return $this->headers[$name][0] ?? null;
    }
    
    /**
     * Get raw response body
     */
    public function body(): string
    {
        return $this->body;
    }
    
    /**
     * Get response body as JSON array
     */
    public function json(): array
    {
        $decoded = json_decode($this->body, true);
        return is_array($decoded) ? $decoded : [];
    }
    
    /**
     * Check if request was successful
     */
    public function successful(): bool
    {
        return $this->statusCode >= 200 && $this->statusCode < 300;
    }
    
    /**
     * Check if request failed
     */
    public function failed(): bool
    {
        return !$this->successful();
    }
    
    /**
     * Get error message if any
     */
    public function error(): ?string
    {
        return $this->error;
    }
    
    /**
     * Convert to array
     */
    public function toArray(): array
    {
        return [
            'status_code' => $this->statusCode,
            'headers' => $this->headers,
            'body' => $this->body,
            'error' => $this->error
        ];
    }
}
