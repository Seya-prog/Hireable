<?php
/**
 * Router — Front Controller for Form Actions
 * 
 * All form POSTs go through: /action/{action.name}
 * .htaccess rewrites /action/... → core/Router.php?action=...
 */

// Bootstrap
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../backend/helpers/session.php';
require_once __DIR__ . '/../backend/helpers/validation.php';
require_once __DIR__ . '/../backend/helpers/csrf.php';

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

/**
 * Simple action-based router
 */
class Router {
    private array $routes = [];

    /**
     * Register a POST route
     */
    public function post(string $action, array $handler): void {
        $this->routes['POST'][$action] = $handler;
    }

    /**
     * Register a GET route
     */
    public function get(string $action, array $handler): void {
        $this->routes['GET'][$action] = $handler;
    }

    /**
     * Dispatch the request to the correct controller method
     */
    public function dispatch(string $action, string $method): void {
        if (!isset($this->routes[$method][$action])) {
            http_response_code(404);
            echo '404 — Route not found: ' . htmlspecialchars($action);
            exit;
        }

        // CSRF validation for all POST actions
        if ($method === 'POST') {
            $token = $_POST['_csrf'] ?? '';
            if (!validateCsrfToken($token)) {
                http_response_code(403);
                setFlash('error', 'Invalid or expired security token. Please try again.');
                $referer = $_SERVER['HTTP_REFERER'] ?? '/';
                header('Location: ' . $referer);
                exit;
            }
        }

        [$controllerClass, $methodName] = $this->routes[$method][$action];

        global $pdo;
        $controller = new $controllerClass($pdo);
        $controller->$methodName();
    }
}

// Build router and load route definitions
$router = new Router();

require __DIR__ . '/../backend/routes/auth.php';
require __DIR__ . '/../backend/routes/jobs.php';
require __DIR__ . '/../backend/routes/applications.php';
require __DIR__ . '/../backend/routes/interviews.php';
require __DIR__ . '/../backend/routes/assessments.php';
require __DIR__ . '/../backend/routes/profile.php';

// Dispatch
$action = $_GET['action'] ?? '';
$method = $_SERVER['REQUEST_METHOD'];
$router->dispatch($action, $method);
