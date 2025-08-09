# Forgr - Simple PHP Function-to-API

**Turn PHP functions into REST APIs with one line of code.**

Write a function, register it, call it as an API.

## Quick Example

```php
<?php
// routes/hello.php
use Forgr\Core\Request;
use Forgr\Core\Response;

function hello(Request $request): Response {
    $data = $request->getBody();
    $name = $data['name'] ?? 'World';
    
    return Response::success(['message' => "Hello {$name}!"]);
}

// Register as API endpoint
post('hello');
```

Call it:
```bash
curl -X POST localhost:8080 \
  -H "X-Route: hello" \
  -d '{"name": "John"}'
```

## Quick Start

```bash
composer create-project forgr/v1 my-api
cd my-api
php -S localhost:8080
```

Test the included example:
```bash
curl -X POST localhost:8080 -H "X-Route: user"
```

## How It Works

1. Create a `.php` file in the `routes/` folder
2. Write a function that takes `Request` and returns `Response`
3. Register it: `get('function_name')` or `post('function_name')`
4. Call via HTTP with `X-Route: function_name` header

## Registration Functions

```php
get('function_name');      // GET route
post('function_name');     // POST route
put('function_name');      // PUT route
delete('function_name');   // DELETE route
route('name', 'PATCH');    // Custom HTTP method
```

## Request & Response API

### Request Methods
```php
$request->getBody()        // JSON body as array
$request->get('key')       // URL parameter
$request->getMethod()      // HTTP method
$request->getBearerToken() // Authorization header
```

### Response Methods
```php
Response::success($data)       // 200 with data
Response::created($data)       // 201 Created
Response::error($msg, $code)   // Error response
Response::notFound()           // 404 Not Found
```

### Response Format
```json
{
  "success": true,
  "message": "Success",
  "data": { "your": "data" },
  "timestamp": "2025-08-09 14:00:00"
}
```

## Features

- ✅ **Zero Configuration** - Works immediately
- ✅ **Function-based** - No classes or frameworks
- ✅ **CORS Ready** - Frontend integration built-in
- ✅ **HTTP Client** - Make external API calls with Guzzle
- ✅ **Consistent JSON** - Structured response format
- ✅ **Error Handling** - Automatic error responses

## Requirements

- PHP 8.1+
- Composer

## License

MIT
