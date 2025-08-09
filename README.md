# Forgr - Minimal API-as-a-Service Platform

The simplest way to turn PHP functions into API endpoints.

## Philosophy

Write functions. Register them. Call them as APIs.

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

## Installation

```bash
composer install
php -S localhost:8080
```

## Test the Examples

```bash
# GET route
curl -X GET http://localhost:8080 -H "X-Route: status"

# POST route
curl -X POST http://localhost:8080 -H "X-Route: hello" -d '{"name": "Forgr"}'

# Echo route  
curl -X POST http://localhost:8080 -H "X-Route: echo_data" -d '{"test": "data"}'
```

## Simple Route Registration

Just use these one-line helpers:

```php
get('function_name');     // GET route
post('function_name');    // POST route  
put('function_name');     // PUT route
delete('function_name');  // DELETE route
route('name', 'PATCH');   // Any method
```

## Adding Your Routes

1. Create a `.php` file in `routes/`
2. Define a function that takes `Request` and returns `Response`
3. Register with one line: `get('function_name')` or `post('function_name')`
4. Call via HTTP with `X-Route: function_name`

**Example:**

```php
<?php
use Forgr\Core\Request;
use Forgr\Core\Response;

function my_api(Request $request): Response {
    $data = $request->get('input');
    return Response::success(['result' => $data * 2]);
}

get('my_api'); // That's it!
```

## Response Helpers

```php
Response::success($data)           // 200 OK
Response::created($data)          // 201 Created  
Response::error($message, $code)  // Error response
Response::notFound()              // 404 Not Found
```

## Request Helpers

```php
$request->get('key')              // Get parameter
$request->getBody()               // Get JSON body
$request->getMethod()             // GET, POST, etc
$request->getBearerToken()        // Authorization header
```

## HTTP Client for External APIs

```php
use Forgr\HTTP\Client;

function external_call(Request $request): Response {
    $client = new Client();
    $data = $client->get('https://api.example.com');
    return Response::success($data);
}

get('external_call');
```

That's it! No complexity, no framework overhead. Just functions → APIs.
