# Forgr - API-as-a-Service Platform

**The simplest way to turn PHP functions into API endpoints.**

Forgr transforms your PHP functions into production-ready APIs with zero configuration. Write a function, register it with one line, and instantly have a REST API endpoint.

## 🚀 Philosophy

**Functions → APIs.** That's it.

```php
// 1. Write a function
function hello(Request $request): Response {
    return Response::success(['message' => 'Hello World']);
}

// 2. Register it (super simple!)
get('hello');

// 3. Call it
// GET http://localhost:8080 -H "X-Route: hello"
```

No frameworks to learn. No complex routing files. No boilerplate code. Just pure PHP functions that become APIs.

## ⚡ Quick Start

```bash
composer install
php -S localhost:8080
```

Test it immediately:

```bash
# GET route
curl -X GET http://localhost:8080 -H "X-Route: status"

# POST route  
curl -X POST http://localhost:8080 -H "X-Route: hello" -d '{"name": "Forgr"}'

# Echo route
curl -X POST http://localhost:8080 -H "X-Route: echo_data" -d '{"test": "data"}'
```

## 🎯 What Makes Forgr Different

### Traditional API Development:
- Set up a framework
- Learn routing systems
- Configure middleware
- Write controllers
- Set up request/response handling
- Deal with CORS, validation, error handling

### With Forgr:
```php
function my_api(Request $request): Response {
    return Response::success(['data' => 'Hello World']);
}
get('my_api');
```

**Done.** You have a working API.

## 🛠️ Core Features

### ✅ **Zero Configuration**
- No config files required
- No framework knowledge needed
- Works out of the box

### ✅ **One-Line Registration**
```php
get('function_name');     // GET route
post('function_name');    // POST route  
put('function_name');     // PUT route
delete('function_name');  // DELETE route
route('name', 'PATCH');   // Any HTTP method
```

### ✅ **Built-in HTTP Client**
Make external API calls easily:
```php
use Forgr\HTTP\Client;

function fetch_data(Request $request): Response {
    $client = new Client();
    $data = $client->get('https://api.example.com');
    return Response::success($data);
}
get('fetch_data');
```

### ✅ **CORS Enabled**
All routes automatically handle CORS for frontend integration.

### ✅ **Request/Response Objects**
Clean, intuitive API for handling HTTP:
```php
// Request helpers
$request->get('key')              // Get parameter
$request->getBody()               // Get JSON body
$request->getMethod()             // HTTP method
$request->getBearerToken()        // Authorization header

// Response helpers  
Response::success($data)          // 200 OK
Response::created($data)          // 201 Created
Response::error($message, $code)  // Error response
Response::notFound()              // 404 Not Found
```

### ✅ **Auto-Discovery**
Just create `.php` files in `routes/` folder. Forgr automatically finds and loads them.

## 📁 Project Structure

```
forgr/
├── src/
│   ├── Core/              # Platform core (App, Request, Response)
│   ├── HTTP/              # HTTP client for external APIs
│   ├── Middleware/        # CORS and other middleware
│   └── helpers.php        # Global helper functions
├── routes/                # Your API functions (auto-discovered)
│   ├── hello.php         # Example POST route
│   ├── status.php        # Example GET route
│   └── echo.php          # Example echo route
├── public/
│   └── index.php         # Single entry point
└── composer.json         # Dependencies
```

## 🔧 Adding Your First Route

1. **Create a file** in `routes/` folder:

```php
<?php
// routes/my-api.php

use Forgr\Core\Request;
use Forgr\Core\Response;

function calculate(Request $request): Response {
    $data = $request->getBody();
    $a = $data['a'] ?? 0;
    $b = $data['b'] ?? 0;
    
    return Response::success([
        'result' => $a + $b,
        'operation' => 'addition'
    ]);
}

// Register it
post('calculate');
```

2. **Test it**:

```bash
curl -X POST http://localhost:8080 \
  -H "X-Route: calculate" \
  -H "Content-Type: application/json" \
  -d '{"a": 5, "b": 3}'
```

3. **Get response**:

```json
{
  "success": true,
  "message": "Success",
  "data": {
    "result": 8,
    "operation": "addition"
  },
  "timestamp": "2025-08-09 14:00:00"
}
```

That's it! You have a working API endpoint.

## 🌐 Real-World Examples

### User Management API
```php
function create_user(Request $request): Response {
    $data = $request->getBody();
    
    // Validate required fields
    if (!isset($data['email']) || !isset($data['name'])) {
        return Response::error('Email and name are required', 400);
    }
    
    // Create user logic here
    $user = [
        'id' => uniqid(),
        'name' => $data['name'],
        'email' => $data['email'],
        'created_at' => date('Y-m-d H:i:s')
    ];
    
    return Response::created($user);
}

post('create_user');
```

### External API Integration
```php
function get_weather(Request $request): Response {
    $city = $request->get('city') ?: 'London';
    
    $client = new Client();
    $weather = $client->get("https://api.weather.com/v1/current?location={$city}");
    
    return Response::success([
        'city' => $city,
        'weather' => $weather
    ]);
}

get('get_weather');
```

### File Upload Handler
```php
function upload_file(Request $request): Response {
    if (!isset($_FILES['file'])) {
        return Response::error('No file uploaded', 400);
    }
    
    $file = $_FILES['file'];
    $uploadDir = __DIR__ . '/../uploads/';
    $fileName = uniqid() . '_' . $file['name'];
    
    if (move_uploaded_file($file['tmp_name'], $uploadDir . $fileName)) {
        return Response::success([
            'filename' => $fileName,
            'size' => $file['size'],
            'url' => '/uploads/' . $fileName
        ]);
    }
    
    return Response::error('Upload failed', 500);
}

post('upload_file');
```

## 🔒 Authentication & Security

Add authentication to any route:

```php
function protected_route(Request $request): Response {
    $token = $request->getBearerToken();
    
    if (!$token || !validateToken($token)) {
        return Response::error('Unauthorized', 401);
    }
    
    return Response::success(['message' => 'Access granted']);
}

function validateToken($token): bool {
    // Your token validation logic
    return $token === 'your-secret-token';
}

get('protected_route');
```

## 🚦 Error Handling

Forgr automatically catches and handles errors:

```php
function risky_operation(Request $request): Response {
    try {
        // Some risky operation
        $result = performRiskyOperation();
        return Response::success($result);
        
    } catch (Exception $e) {
        // Forgr will catch this and return a proper error response
        throw $e;
    }
}

post('risky_operation');
```

## 🔄 HTTP Methods

Support all HTTP methods:

```php
get('read_data');        // GET
post('create_data');     // POST  
put('update_data');      // PUT
delete('delete_data');   // DELETE
route('patch_data', 'PATCH');  // PATCH
```

## 📝 Request/Response Format

### Request Format
Call any route using the `X-Route` header:

```bash
curl -X POST http://localhost:8080 \
  -H "X-Route: your_function_name" \
  -H "Content-Type: application/json" \
  -d '{"key": "value"}'
```

### Response Format
All responses follow a consistent format:

```json
{
  "success": true,
  "message": "Success",
  "data": {
    "your": "data"
  },
  "timestamp": "2025-08-09 14:00:00"
}
```

## 🎛️ Advanced Configuration

For advanced users, you can still configure routes manually:

```php
function advanced_route(Request $request): Response {
    return Response::success(['data' => 'advanced']);
}

// Manual configuration
App::getInstance()->register('advanced_route', [
    'method' => 'POST',
    'cors' => true,
    'protected' => false
]);
```

## 🚀 Production Deployment

### With Apache
Create `.htaccess` in your web root:
```apache
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php [QSA,L]
```

### With Nginx
```nginx
location / {
    try_files $uri $uri/ /index.php?$query_string;
}
```

### With Docker
```dockerfile
FROM php:8.1-apache
COPY . /var/www/html/
RUN composer install --no-dev
EXPOSE 80
```

## 🤝 Why Choose Forgr?

- **🎯 Focus on Logic**: Write business logic, not boilerplate
- **⚡ Instant APIs**: Function → API in seconds  
- **🧩 No Learning Curve**: Just PHP functions
- **🔧 Lightweight**: Minimal dependencies
- **🌐 Production Ready**: CORS, error handling, structured responses
- **🚀 Scalable**: Add routes without touching core code

## 📦 Requirements

- PHP 8.1+
- Composer
- Basic PHP knowledge

## 🛣️ Roadmap

- [ ] Built-in validation system
- [ ] Rate limiting middleware
- [ ] OpenAPI documentation generation  
- [ ] Database integration helpers
- [ ] Caching middleware
- [ ] WebSocket support

## 📄 License

MIT License - Build amazing APIs!

---

**Forgr: Because APIs should be as simple as writing functions.**

*Turn any PHP function into a REST API endpoint in seconds. No frameworks. No complexity. Just pure simplicity.*
