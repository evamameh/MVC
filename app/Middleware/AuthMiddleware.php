<?php
declare(strict_types=1);







namespace App\Middleware;

final class AuthMiddleware
{
    
    private static array $config = []; 

    public static function setConfig(array $config): void
    {
        self::$config = $config;
    }

    
    public static function ensureStarted(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    
    public static function requireSession(): void
    {
        self::ensureStarted();

        $path = self::normalizedPath();
        $publicPaths = ['/login', '/register', '/logout'];

        if (in_array($path, $publicPaths, true)) {
            return; 
        }

        if (isset($_SESSION['user_id'])) {
            return; 
        }

        header('Location: ' . self::loginUrl());
        exit;
    }

    
    private static function normalizedPath(): string
    {
        $path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
        $scriptName = (string) ($_SERVER['SCRIPT_NAME'] ?? '');
        $scriptDir = rtrim(str_replace('\\', '/', dirname($scriptName)), '/');

        if ($scriptDir !== '' && $scriptDir !== '.' && str_starts_with($path, $scriptDir)) {
            $path = substr($path, strlen($scriptDir));
            if ($path === '') {
                $path = '/';
            }
        }

        if ($path === '/index.php') {
            $path = '/';
        } elseif (str_starts_with($path, '/index.php/')) {
            $path = substr($path, strlen('/index.php'));
        }

        $path = rtrim($path, '/');

        return $path === '' ? '/' : $path;
    }

    private static function loginUrl(): string
    {
        $base = rtrim((string) (self::$config['base_path'] ?? ''), '/');

        $requestPath = (string) (parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH) ?? '');
        $usesIndexPhp = str_contains($requestPath, '/index.php/') || str_ends_with($requestPath, '/index.php');

        if ($usesIndexPhp && !str_ends_with($base, '/index.php')) {
            $base .= '/index.php';
        }

        return $base . '/login';
    }
}
