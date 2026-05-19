<?php
declare(strict_types=1);







namespace Core\View;

final class Engine
{
    private string $viewsPath; 
    private string $urlBase;  

    public function __construct(string $viewsPath, string $urlBase = '')
    {
        $this->viewsPath = $viewsPath;
        $this->urlBase = $urlBase;
    }

    public function render(string $view, array $data = []): string
    {
        
        $path = $this->viewsPath . DIRECTORY_SEPARATOR . str_replace('.', DIRECTORY_SEPARATOR, $view) . '.php';
        if (!is_file($path)) {
            throw new \RuntimeException('View not found: ' . $view);
        }

        
        $data['base'] = $this->resolveBasePath();

        extract($data, EXTR_SKIP); 
        ob_start();                 
        if (!defined('MVC_VIEW_VIA_ENGINE')) {
            define('MVC_VIEW_VIA_ENGINE', true);
        }
        
        include $path; 

        return (string) ob_get_clean(); 
    }

    
    private function resolveBasePath(): string
    {
        $base = rtrim($this->urlBase, '/');
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
