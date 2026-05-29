<?php
declare(strict_types=1);

namespace App\Middleware;

final class Auth
{
    public function handle(): void
    {
        $path = (string) (parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? '/');

        if ($this->isProtectedPath($path)) {
            header('Location: /MVC/public/dashboard');
            exit;
        }
    }

    private function isProtectedPath(string $path): bool
    {
        return preg_match('#/MVC/(app|core|config|routes|vendor)(/|$)#i', $path) === 1;
    }
}
