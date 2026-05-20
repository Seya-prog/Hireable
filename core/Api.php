<?php
/**
 * API Handler — JSON Endpoint for AJAX Requests
 * 
 * All AJAX calls go through: /api/{endpoint}
 * .htaccess rewrites /api/... → core/Api.php?endpoint=...
 * 
 * Returns JSON responses only. No redirects, no HTML.
 */

// Bootstrap
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../backend/helpers/session.php';

// Autoload controllers
spl_autoload_register(function ($class) {
    $file = __DIR__ . '/../backend/controllers/' . $class . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

// Autoload repositories
spl_autoload_register(function ($class) {
    $file = __DIR__ . '/../database/repositories/' . $class . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

// Set JSON headers
header('Content-Type: application/json; charset=utf-8');
header('X-Content-Type-Options: nosniff');

// Reuse the Router class
class ApiRouter {
    private array $routes = [];

    public function post(string $endpoint, array $handler): void {
        $this->routes['POST'][$endpoint] = $handler;
    }

    public function get(string $endpoint, array $handler): void {
        $this->routes['GET'][$endpoint] = $handler;
    }

    public function dispatch(string $endpoint, string $method): void {
        if (!isset($this->routes[$method][$endpoint])) {
            http_response_code(404);
            echo json_encode(['success' => false, 'error' => 'Endpoint not found']);
            exit;
        }

        [$controllerClass, $methodName] = $this->routes[$method][$endpoint];

        global $pdo;
        $controller = new $controllerClass($pdo);

        try {
            $controller->$methodName();
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Internal server error']);
        }
    }
}

// Build router and load API routes
$router = new ApiRouter();
require __DIR__ . '/../backend/routes/api.php';

// Dispatch
$endpoint = $_GET['endpoint'] ?? '';
$method = $_SERVER['REQUEST_METHOD'];
$router->dispatch($endpoint, $method);
