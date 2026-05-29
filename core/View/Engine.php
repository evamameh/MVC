<?php
declare(strict_types=1);

namespace Core\View;

final class Engine {
    private string $viewsPath;

    private string $urlBase;

    public function __construct(string $viewsPath, string $urlBase = '') {
        $this->viewsPath = $viewsPath;
        $this->urlBase = $urlBase;
    }

    public function render(string $view, array $data = []): string {
        $path = $this->viewsPath . DIRECTORY_SEPARATOR . str_replace('.', DIRECTORY_SEPARATOR, $view) . '.php';

        if (!is_file($path)) {
            throw new \RuntimeException('View not found: ' . $view);
        }

        $data['base'] = $this->resolveBasePath();

        extract($data, EXTR_SKIP);

        ob_start();

        include $path;
        
        return (string) ob_get_clean();
    }

    private function resolveBasePath(): string {
        return rtrim($this->urlBase, '/');
    }
}
