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
$request->getQuery('key')       // URL parameter
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
- ✅ **Enhanced Error Handling** - Detailed debugging information
- ✅ **File Inclusion System** - Easy inclusion of external PHP files

## Enhanced Error Handling

Forgr now provides detailed error information to help with debugging while maintaining security in production.

### Debug Mode
Enable detailed error reporting by setting the environment variable:
```bash
FORGR_DEBUG=true
```

In debug mode, errors include:
- Exception message and type
- File and line number
- Stack trace (limited to 10 frames)
- Request context (method, URI, route header)

### Production Mode
When `FORGR_DEBUG=false` or not set, only basic error messages are returned without sensitive information.

### Example Error Response (Debug Mode)
```json
{
  "success": false,
  "error": "Route execution failed: Call to undefined function undefined_function_call()",
  "timestamp": "2025-09-15 14:13:16",
  "debug": {
    "exception_message": "Call to undefined function undefined_function_call()",
    "exception_type": "Error",
    "file": "/path/to/routes/test_error.php",
    "line": 9,
    "stack_trace": [...],
    "context": {
      "request_method": "GET",
      "request_uri": "/",
      "route_header": "test_error"
    }
  }
}
```

## File Inclusion System

Forgr provides helper functions to easily include external PHP files from any directory without path issues.

### Helper Functions

```php
// Include a file relative to project root
forgr_include('path/to/file.php');

// Require a file relative to project root
forgr_require('path/to/file.php');

// Require once a file relative to project root
forgr_require_once('path/to/file.php');

// Get project root path
$root = forgr_root();

// Get absolute path relative to project root
$path = forgr_path('config/database.php');
```

### Example Usage in Routes

```php
<?php
use Forgr\Core\Request;
use Forgr\Core\Response;

// Include external SQL helper
forgr_require_once('sql_helper.php');

function my_route(Request $request): Response
{
    $connection = getDbConnection();
    $result = sql_query("SELECT * FROM users");
    
    return Response::success([
        'connection' => $connection,
        'data' => $result
    ]);
}

post('my_route');
```

## Requirements

- PHP 8.1+
- Composer

## Configuration

Create a `.env` file in your project root for configuration:

```bash
# Debug mode - set to true for detailed error reporting
FORGR_DEBUG=true

# App environment
APP_ENV=development

# Error logging level
LOG_LEVEL=debug
```

**Important:** Set `FORGR_DEBUG=false` in production to avoid exposing sensitive information.

## Best Practices

### File Organization
```
my-api/
├── routes/          # Your API functions
│   ├── user.php
│   └── auth.php
├── helpers/         # Shared PHP files
│   ├── database.php
│   └── utils.php
├── config/          # Configuration files
└── .env            # Environment variables
```

### Including External Files
Always use the forgr helper functions for file inclusion:

```php
// ✅ Good - uses helper function
forgr_require_once('helpers/database.php');

// ❌ Bad - relative path issues
require_once '../helpers/database.php';
```

### Error Handling
Let Forgr handle errors automatically, but you can also create custom error responses:

```php
function my_route(Request $request): Response
{
    $data = $request->getBody();
    
    if (empty($data['email'])) {
        return Response::error('Email is required', 400);
    }
    
    // Your logic here
    return Response::success($result);
}
```

## License

MIT
