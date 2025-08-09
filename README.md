# Forgr - Minimal API-as-a-Service Platform

The simplest way to turn PHP functions into API endpoints.

## Philosophy

Write functions. Register them. Call them as APIs.

```php
// 1. Write a function
function hello(Request $request): Response {
    return Response::success(['message' => 'Hello World']);
}

// 2. Register it  
\Forgr\Core\App::getInstance()->register('hello', ['method' => 'GET']);

// 3. Call it
// GET http://localhost:8080 -H "X-Route: hello"
```

## Installation

```bash
composer install
php -S localhost:8080
```

## Test the Example

```bash
curl -X GET http://localhost:8080 -H "X-Route: hello" -d '{"name": "Forgr"}'
```

## Adding Your Routes

1. Create PHP files in `routes/`
2. Define functions that take `Request` and return `Response`  
3. Register them with the app
4. Use via HTTP with `X-Route` header

## Minimal Structure

```
forgr/
├── src/
│   ├── Core/          # Platform core (App, Request, Response)
│   ├── HTTP/          # HTTP client for external calls
│   └── Middleware/    # CORS middleware
├── routes/            # Your API functions
└── index.php          # Single entry point
```

## Response Helpers

```php
Response::success($data)           // 200 OK
Response::created($data)          // 201 Created  
Response::error($message, $code)  // Error response
Response::notFound()              // 404 Not Found
```

## HTTP Client

```php
use Forgr\HTTP\Client;

$client = new Client();
$data = $client->get('https://api.example.com');
```

That's it. No database coupling, no auth assumptions, no CLI overhead. 

Just functions → APIs.
