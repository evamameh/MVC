<?php
declare(strict_types=1);

namespace Core\Http;

final class Request {

    public function __construct(
        public readonly string $uri,
        public readonly string $method
    ) {
    }

    public static function fromGlobals(): self {
        return new self(
            $_SERVER['REQUEST_URI'] ?? '/',
            $_SERVER['REQUEST_METHOD'] ?? 'GET'
        );
    }

    public function getMethod(): string {
        return $this->method;
    }

    public function getPath(): string {
        $path = parse_url($this->uri, PHP_URL_PATH) ?: '/';
        $scriptName = (string) ($_SERVER['SCRIPT_NAME'] ?? '');
        $scriptDir = rtrim(str_replace('\\', '/', dirname($scriptName)), '/');

        if ($scriptDir !== '' && $scriptDir !== '.' && str_starts_with($path, $scriptDir)) {
            $path = substr($path, strlen($scriptDir));
            if ($path === '') {
                $path = '/';
            }
        }

        $path = rtrim($path, '/');

        return $path === '' ? '/' : $path;
    }
}
