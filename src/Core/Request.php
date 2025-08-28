<?php

namespace Forgr\Core;

/**
 * HTTP Request wrapper for Forgr API routes
 */
class Request
{
    private array $query;
    private array $body;
    private array $headers;
    private array $files;
    private array $routeConfig = [];
    private ?object $user = null;

    public function __construct()
    {
        $this->query = $_GET ?? [];
        $this->headers = $this->getAllHeaders();
        $this->files = $_FILES ?? [];
        $this->parseBody();
    }

    /**
     * Get HTTP method
     */
    public function getMethod(): string
    {
        return $_SERVER['REQUEST_METHOD'] ?? 'GET';
    }

    /**
     * Get request path
     */
    public function getPath(): string
    {
        return $_SERVER['REQUEST_URI'] ?? '/';
    }

    /**
     * Get query parameter
     */
    public function getQuery(string $key = null, mixed $default = null): mixed
    {
        if ($key === null) {
            return $this->query;
        }
        return $this->query[$key] ?? $default;
    }

    /**
     * Get body parameter (POST data or JSON)
     */
    public function get(string $key = null, mixed $default = null): mixed
    {
        if ($key === null) {
            return $this->body;
        }
        return $this->body[$key] ?? $default;
    }

    /**
     * Get request body as array
     */
    public function getBody(): array
    {
        return $this->body;
    }

    /**
     * Get request header
     */
    public function getHeader(string $key, mixed $default = null): mixed
    {
        $key = strtolower(str_replace('_', '-', $key));
        return $this->headers[$key] ?? $default;
    }

    /**
     * Get all headers
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * Get Bearer token from Authorization header
     */
    public function getBearerToken(): ?string
    {
        $auth = $this->getHeader('authorization');

        if ($auth && str_starts_with($auth, 'Bearer ')) {
            return substr($auth, 7);
        }

        return null;
    }

    /**
     * Get uploaded file
     */
    public function getFile(string $key): ?UploadedFile
    {
        if (!isset($this->files[$key])) {
            return null;
        }

        $file = $this->files[$key];
        return new UploadedFile($file);
    }

    /**
     * Get all uploaded files
     */
    public function getFiles(): array
    {
        $files = [];
        foreach ($this->files as $key => $file) {
            $files[$key] = new UploadedFile($file);
        }
        return $files;
    }

    /**
     * Get client IP address
     */
    public function getClientIP(): string
    {
        $headers = ['X-Forwarded-For', 'X-Real-IP', 'HTTP_CLIENT_IP'];

        foreach ($headers as $header) {
            $ip = $this->getHeader($header);
            if ($ip) {
                return explode(',', $ip)[0];
            }
        }

        return $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
    }

    /**
     * Check if route is protected
     */
    public function isProtected(): bool
    {
        return $this->routeConfig['protected'] ?? false;
    }

    /**
     * Set route configuration
     */
    public function setRouteConfig(array $config): void
    {
        $this->routeConfig = $config;
    }

    /**
     * Get authenticated user
     */
    public function getUser(): ?object
    {
        return $this->user;
    }

    /**
     * Set authenticated user
     */
    public function setUser(object $user): void
    {
        $this->user = $user;
    }

    /**
     * Get route name from X-Route header or route parameter
     */
    // public function getRouteName(): ?string
    // {
    //     // Try X-Route header first
    //     $route = $this->getHeader('x-route');
    //     if ($route) {
    //         return $route;
    //     }

    //     // Try route parameter in body
    //     return $this->get('route');
    // }




    public function getRouteName(): ?string
    {
       return resolveRoutePath($this->getPath());
    }




    private function parseBody(): void
    {
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
        $input = file_get_contents('php://input');

        if (str_contains($contentType, 'application/json')) {
            $this->body = json_decode($input, true) ?? [];
        } elseif (str_contains($contentType, 'application/x-www-form-urlencoded')) {
            parse_str($input, $this->body);
        } else {
            $this->body = $_POST ?? [];
        }
    }

    private function getAllHeaders(): array
    {
        $headers = [];

        if (function_exists('getallheaders')) {
            $rawHeaders = getallheaders();
            foreach ($rawHeaders as $key => $value) {
                $headers[strtolower($key)] = $value;
            }
        } else {
            // Fallback for nginx
            foreach ($_SERVER as $key => $value) {
                if (str_starts_with($key, 'HTTP_')) {
                    $header = strtolower(str_replace(['HTTP_', '_'], ['', '-'], $key));
                    $headers[$header] = $value;
                }
            }
        }

        return $headers;
    }
}

/**
 * Uploaded file wrapper
 */
class UploadedFile
{
    private array $file;

    public function __construct(array $file)
    {
        $this->file = $file;
    }

    public function getName(): string
    {
        return $this->file['name'] ?? '';
    }

    public function getMimeType(): string
    {
        return $this->file['type'] ?? '';
    }

    public function getSize(): int
    {
        return $this->file['size'] ?? 0;
    }

    public function getTempPath(): string
    {
        return $this->file['tmp_name'] ?? '';
    }

    public function getError(): int
    {
        return $this->file['error'] ?? UPLOAD_ERR_NO_FILE;
    }

    public function getContent(): string
    {
        if ($this->getError() !== UPLOAD_ERR_OK) {
            return '';
        }

        return file_get_contents($this->getTempPath()) ?: '';
    }

    public function getHashName(string $extension = null): string
    {
        if ($extension === null) {
            $extension = pathinfo($this->getName(), PATHINFO_EXTENSION);
        }

        return hash('sha256', $this->getName() . time()) . '.' . $extension;
    }

    public function moveTo(string $destination): bool
    {
        return move_uploaded_file($this->getTempPath(), $destination);
    }
}
