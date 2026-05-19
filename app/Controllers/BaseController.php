<?php
declare(strict_types=1);

namespace App\Controllers;

use Core\Http\Response;
use Core\View\Engine;

abstract class BaseController
{
    protected Engine $view; 

    public function __construct(Engine $view)
    {
        $this->view = $view;
    }

    
    protected function render(string $view, array $data = []): void
    {
        echo $this->view->render($view, $data);
    }

    
    protected function redirect(string $url): never
    {
        if (preg_match('#^https?://#i', $url) === 1) {
            Response::redirect($url);
        }

        $base = $this->resolveBasePath();
        $target = str_starts_with($url, '/') ? $base . $url : $url;
        Response::redirect($target);
    }

    
    protected function json(array $data, int $statusCode = 200): never
    {
        Response::json($data, $statusCode);
    }

    
    protected function setFlash(string $key, string $message): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $_SESSION['flash'][$key] = $message; 
    }

    
    protected function getFlash(string $key): ?string
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $value = $_SESSION['flash'][$key] ?? null;
        unset($_SESSION['flash'][$key]);

        return $value === null ? null : (string) $value;
    }

    
    private function resolveBasePath(): string
    {
        $config = require dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'app.php';
        $base = rtrim((string) ($config['base_path'] ?? ''), '/');

        $requestPath = (string) (parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH) ?? '');
        $usesIndexPhp =
            str_contains($requestPath, '/index.php/')
            || str_ends_with($requestPath, '/index.php');

        if ($usesIndexPhp && !str_ends_with($base, '/index.php')) {
            $base .= '/index.php';
        }

        return $base;
    }
}
