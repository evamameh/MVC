<?php
declare(strict_types=1);

namespace Core\Http;

final class Request
{
    
    public static function fromGlobals(): self
    {
        return new self();
    }

    
    public function getMethod(): string
    {
        return $_SERVER['REQUEST_METHOD'] ?? 'GET';
    }

    
    public function getPath(): string
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

    
    public function getParsedBody(): array
    {
        $contentType = $this->getContentType();
        if (str_contains(strtolower($contentType), 'application/json')) {
            $raw = file_get_contents('php://input') ?: '';
            $decoded = json_decode($raw, true);

            return is_array($decoded) ? $decoded : [];
        }

        return $_POST;
    }

    public function getContentType(): string
    {
        return $_SERVER['CONTENT_TYPE'] ?? ($_SERVER['HTTP_CONTENT_TYPE'] ?? '');
    }
}
