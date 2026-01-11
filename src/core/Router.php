<?php

namespace App\Core;

class Router
{
    private array $routes = [];
    private array $middleware = [];

    /**
     * Add a GET route
     */
    public function get(string $path, string $controller, string $method, array $middleware = []): void
    {
        $this->addRoute('GET', $path, $controller, $method, $middleware);
    }

    /**
     * Add a POST route
     */
    public function post(string $path, string $controller, string $method, array $middleware = []): void
    {
        $this->addRoute('POST', $path, $controller, $method, $middleware);
    }

    /**
     * Add a route to the routing table
     */
    private function addRoute(string $httpMethod, string $path, string $controller, string $method, array $middleware): void
    {
        $this->routes[] = [
            'method' => $httpMethod,
            'path' => $path,
            'controller' => $controller,
            'action' => $method,
            'middleware' => $middleware
        ];
    }

    /**
     * Register middleware
     */
    public function registerMiddleware(string $name, $middlewareClass): void
    {
        $this->middleware[$name] = $middlewareClass;
    }

    /**
     * Dispatch the request to the appropriate controller
     */
    public function dispatch(string $uri, string $requestMethod): void
    {
        // Remove query string from URI
        $uri = parse_url($uri, PHP_URL_PATH);

        // Remove trailing slash except for root
        if ($uri !== '/' && substr($uri, -1) === '/') {
            $uri = rtrim($uri, '/');
        }

        foreach ($this->routes as $route) {
            if ($route['method'] !== $requestMethod) {
                continue;
            }

            $pattern = $this->convertPathToRegex($route['path']);

            if (preg_match($pattern, $uri, $matches)) {
                array_shift($matches); // Remove full match

                // Execute middleware
                foreach ($route['middleware'] as $middlewareName) {
                    if (isset($this->middleware[$middlewareName])) {
                        $middlewareInstance = new $this->middleware[$middlewareName]();
                        $result = $middlewareInstance->handle();

                        if ($result === false) {
                            return; // Middleware stopped execution
                        }
                    }
                }

                // Execute controller action
                $this->executeController($route['controller'], $route['action'], $matches);
                return;
            }
        }

        // 404 Not Found
        $this->sendNotFound();
    }

    /**
     * Convert route path to regex pattern
     */
    private function convertPathToRegex(string $path): string
    {
        // Convert {param} to regex capture group
        $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '([a-zA-Z0-9_-]+)', $path);
        return '#^' . $pattern . '$#';
    }

    /**
     * Execute the controller action
     */
    private function executeController(string $controllerName, string $action, array $params): void
    {
        $controllerClass = "App\\Controllers\\{$controllerName}";

        if (!class_exists($controllerClass)) {
            $this->sendNotFound();
            return;
        }

        $controller = new $controllerClass();

        if (!method_exists($controller, $action)) {
            $this->sendNotFound();
            return;
        }

        call_user_func_array([$controller, $action], $params);
    }

    /**
     * Send 404 response
     */
    private function sendNotFound(): void
    {
        http_response_code(404);
        echo "404 - Page Not Found";
        exit;
    }

    /**
     * Get all registered routes 
     */
    public function getRoutes(): array
    {
        return $this->routes;
    }
}
