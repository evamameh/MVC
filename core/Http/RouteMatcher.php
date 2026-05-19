<?php
declare(strict_types=1);

namespace Core\Http;

class RouteMatcher
{
    
    public function match(array $routes, string $requestMethod, string $requestPath): ?array
    {
        foreach ($routes as $route) {
            
            $pattern = preg_replace('/\{(\w+)\}/', '(?P<$1>[^/]+)', $route['path']);
            $pattern = '@^' . $pattern . '$@D';

            $methodMatches = $route['method'] === strtoupper($requestMethod);
            $pathMatches = preg_match($pattern, $requestPath, $matches) === 1;

            if ($methodMatches && $pathMatches) {
                
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);

                return [
                    'controller' => $route['controller'], 
                    'action' => $route['action'],           
                    'params' => $params,                    
                    'middleware' => $route['middleware'] ?? [],
                ];
            }
        }

        return null; 
    }
}
