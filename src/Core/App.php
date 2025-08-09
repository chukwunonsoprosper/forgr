<?php

namespace Forgr\Core;

/**
 * Forgr API-as-a-Service Platform
 * Main application class for managing routes and services
 */
class App
{
    private static ?App $instance = null;
    private static array $routes = [];
    private array $config = [];
    private string $routesPath;
    private static bool $routesDiscovered = false;
    
    public function __construct(string $routesPath = 'routes')
    {
        $this->routesPath = $routesPath;
        $this->loadConfig();
        
        // Only discover routes once
        if (!self::$routesDiscovered) {
            $this->discoverRoutes();
            self::$routesDiscovered = true;
        }
    }
    
    public static function getInstance(): App
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Register a function as an API endpoint
     */
    public function register(string $functionName, ?array $config = null): void
    {
        $reflection = new \ReflectionFunction($functionName);
        
        // Use function name directly as route name - much simpler!
        $routeName = $functionName;
        
        // Use provided config or defaults
        $routeConfig = $config ?? $this->getDefaultConfig();
        
        self::$routes[$routeName] = [
            'function' => $functionName,
            'config' => $routeConfig,
            'reflection' => $reflection
        ];
        
        error_log("Registered route: {$routeName} -> {$functionName}()");
        error_log("Current routes count: " . count(self::$routes));
    }
    
    /**
     * Execute a route based on the request
     */
    public function execute(string $routeName): Response
    {
        if (!isset(self::$routes[$routeName])) {
            return Response::notFound("Route '{$routeName}' not found");
        }
        
        $route = self::$routes[$routeName];
        $config = $route['config'];
        
        // Create request object
        $request = new Request();
        $request->setRouteConfig($config);
        
        // Check HTTP method
        if (isset($config['method']) && $config['method'] !== $request->getMethod()) {
            return Response::error("Method {$request->getMethod()} not allowed", 405);
        }
        
        // Execute the function
        try {
            $function = $route['function'];
            $response = $function($request);
            
            // Ensure we have a Response object
            if (!$response instanceof Response) {
                $response = Response::json($response);
            }
            
            return $response;
            
        } catch (\Exception $e) {
            error_log("Route execution error: " . $e->getMessage());
            return Response::error('Internal server error', 500);
        }
    }
    
    /**
     * Get all registered routes
     */
    public function getRoutes(): array
    {
        error_log("getRoutes called, routes count: " . count(self::$routes));
        error_log("Routes: " . print_r(array_keys(self::$routes), true));
        return array_keys(self::$routes);
    }
    
    /**
     * Get route information
     */
    public function getRouteInfo(string $routeName): ?array
    {
        return self::$routes[$routeName] ?? null;
    }
    
    private function loadConfig(): void
    {
        $configFile = __DIR__ . '/../../config/app.php';
        if (file_exists($configFile)) {
            $this->config = require $configFile;
        }
    }
    
    private function discoverRoutes(): void
    {
        $routesDir = __DIR__ . '/../../' . $this->routesPath;
        if (!is_dir($routesDir)) {
            return;
        }
        
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($routesDir)
        );
        
        foreach ($iterator as $file) {
            if ($file->isFile() && $file->getExtension() === 'php') {
                require_once $file->getRealPath();
            }
        }
    }
    
    private function extractRouteNameFromFile(string $filename): string
    {
        $routesPath = realpath(__DIR__ . '/../../' . $this->routesPath);
        $relativePath = str_replace($routesPath . '/', '', $filename);
        $routeName = str_replace('.php', '', $relativePath);
        $finalRoute = str_replace('/', '-', $routeName);
        
        error_log("Route extraction: {$filename} -> {$relativePath} -> {$routeName} -> {$finalRoute}");
        
        return $finalRoute;
    }
    
    private function loadRouteConfig(string $routeName): array
    {
        $configFile = __DIR__ . '/../../config/routes/' . $routeName . '.php';
        
        if (file_exists($configFile)) {
            return require $configFile;
        }
        
        return $this->getDefaultConfig();
    }

    private function getDefaultConfig(): array
    {
        return [
            'method' => 'GET',
            'protected' => false,
            'cors' => true,
            'rate_limit' => null
        ];
    }
}
