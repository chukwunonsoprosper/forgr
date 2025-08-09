# Forgr - API-as-a-Service Platform

**Turn PHP functions into REST APIs instantly.**

Write a function, register it with one line, call it as an API. No frameworks, no configuration, no complexity.

## Philosophy: Functions → APIs

```php
// 1. Write a function
function hello(Request $request): Response {
    return Response::success(['message' => 'Hello World']);
}

// 2. Register it
get('hello');

// 3. Call it
// curl -X GET localhost:8080 -H "X-Route: hello"
```

## Quick Start

```bash
composer install
php -S localhost:8080
```

Test the examples:
```bash
curl -X GET localhost:8080 -H "X-Route: status"
curl -X POST localhost:8080 -H "X-Route: hello" -d '{"name":"World"}'
curl -X POST localhost:8080 -H "X-Route: echo_data" -d '{"test":"data"}'
```

## How It Works

1. **Create** a `.php` file in `routes/`
2. **Write** a function that takes `Request` and returns `Response`  
3. **Register** with: `get('function_name')` or `post('function_name')`
4. **Call** via HTTP with `X-Route: function_name`

### Example Route
```php
<?php
// routes/calculator.php
use Forgr\Core\Request;
use Forgr\Core\Response;

function add_numbers(Request $request): Response {
    $data = $request->getBody();
    $result = ($data['a'] ?? 0) + ($data['b'] ?? 0);
    
    return Response::success(['result' => $result]);
}

post('add_numbers');
```

Test it:
```bash
curl -X POST localhost:8080 \
  -H "X-Route: add_numbers" \
  -d '{"a": 5, "b": 3}'
```

## Registration Helpers

```php
get('function_name');      // GET route
post('function_name');     // POST route  
put('function_name');      // PUT route
delete('function_name');   // DELETE route
route('name', 'PATCH');    // Any HTTP method
```

## Request & Response

### Request Object
```php
$request->get('key')           // Get parameter
$request->getBody()            // JSON body as array
$request->getMethod()          // HTTP method
$request->getBearerToken()     // Authorization: Bearer token
```

### Response Helpers
```php
Response::success($data)       // 200 OK
Response::created($data)       // 201 Created  
Response::error($msg, $code)   // Error response
Response::notFound()           // 404 Not Found
```

### Response Format
All responses follow this structure:
```json
{
  "success": true,
  "message": "Success", 
  "data": { "your": "data" },
  "timestamp": "2025-08-09 14:00:00"
}
```

## Built-in Features

### HTTP Client for External APIs
```php
use Forgr\HTTP\Client;

function fetch_data(Request $request): Response {
    $client = new Client();
    $data = $client->get('https://api.example.com');
    return Response::success($data);
}

get('fetch_data');
```

### Authentication Example
```php
function secure_route(Request $request): Response {
    $token = $request->getBearerToken();
    
    if (!$token || $token !== 'secret-key') {
        return Response::error('Unauthorized', 401);
    }
    
    return Response::success(['message' => 'Access granted']);
}

get('secure_route');
```

### File Upload Example
```php
function upload(Request $request): Response {
    if (!isset($_FILES['file'])) {
        return Response::error('No file uploaded', 400);
    }
    
    $file = $_FILES['file'];
    // Handle file upload logic here
    
    return Response::success(['filename' => $file['name']]);
}

post('upload');
```

## Project Structure

```
forgr/
├── src/Core/           # App, Request, Response classes
├── src/HTTP/           # HTTP client for external APIs  
├── src/Middleware/     # CORS handling
├── src/helpers.php     # Global helper functions
├── routes/             # Your API functions
├── index.php           # Entry point
└── composer.json       # Dependencies
```

## Production Deployment

### Apache (.htaccess)
```apache
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteRule ^(.*)$ index.php [QSA,L]
```

### Nginx
```nginx
location / {
    try_files $uri $uri/ /index.php?$query_string;
}
```

### Docker
```dockerfile
FROM php:8.1-apache
COPY . /var/www/html/
RUN composer install --no-dev
EXPOSE 80
```

## Why Forgr?

- ✅ **Zero Configuration** - Works out of the box
- ✅ **No Framework** - Just pure PHP functions  
- ✅ **Auto CORS** - Frontend integration ready
- ✅ **Built-in HTTP Client** - Call external APIs easily
- ✅ **Structured Responses** - Consistent JSON format
- ✅ **Auto-Discovery** - Drop files in routes/ folder
- ✅ **Production Ready** - Error handling, logging, CORS

## Requirements

- PHP 8.1+
- Composer

## License

MIT - Build amazing APIs!

---

**Forgr: The simplest way to build REST APIs in PHP.**
