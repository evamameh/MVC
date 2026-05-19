<?php
declare(strict_types=1);

namespace Core\Http;

use Core\Container\Container;

final class Dispatcher
{
    private RouteMatcher $matcher;
    private Container $container;

    public function __construct(RouteMatcher $matcher, Container $container)
    {
        $this->matcher = $matcher;
        $this->container = $container;
    }

    public function handle(Request $request, array $routes): void
    {
        
        $match = $this->matcher->match($routes, $request->getMethod(), $request->getPath());

        
        if ($match === null) {
            
            if ($this->shouldRedirectGuestToLogin($request->getPath())) {
                header('Location: ' . $this->buildLoginUrl());
                exit;
            }

            http_response_code(404);
            echo '404 Not Found';

            return;
        }

        
        foreach ($match['middleware'] as $middleware) {
            if (is_callable($middleware)) {
                $middleware();
            }
        }
        
        $controller = $this->container->get($match['controller']);
        $action = $match['action'];
        $controller->$action(...array_values($match['params']));
    }

    
    private function shouldRedirectGuestToLogin(string $path): bool
    {
        $publicPaths = ['/login', '/register', '/logout'];
        if (in_array($path, $publicPaths, true)) {
            return false;
        }

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        return !isset($_SESSION['user_id']);
    }

    
    private function buildLoginUrl(): string
    {
        $scriptName = str_replace('\\', '/', (string) ($_SERVER['SCRIPT_NAME'] ?? ''));
        $base = rtrim(dirname($scriptName), '/');
        if ($base === '.' || $base === DIRECTORY_SEPARATOR) {
            $base = '';
        }

        $requestPath = (string) (parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH) ?? '');
        $usesIndexPhp = str_contains($requestPath, '/index.php/');

        $prefix = $base;
        if ($usesIndexPhp && !str_ends_with($prefix, '/index.php')) {
            $prefix .= '/index.php';
        }

        return $prefix . '/login';
    }
}
