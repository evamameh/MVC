<?php
declare(strict_types=1);

namespace Core\Http;

use Core\Container\Container;
use ReflectionMethod;
use ReflectionNamedType;

final class Dispatcher {
    private Router $router;

    private Container $container;

    public function __construct(Router $router, Container $container) {
        $this->router = $router;
        $this->container = $container;
    }

    public function handle(Request $request, array $routes): void {
        $match = $this->router->match($routes, $request->getMethod(), $request->getPath());

        if ($match === null) {
            http_response_code(404);
            echo '404 Not Found';
            return;
        }

        $controller = $this->container->get($match['controller']);

        $this->invokeAction($controller, $match['action'], $match['params']);
    }

    private function invokeAction(object $controller, string $action, array $routeParams): void {
        $reflection = new ReflectionMethod($controller, $action);

        $positional = array_values($routeParams);
        $arguments = [];

        foreach ($reflection->getParameters() as $index => $parameter) {
            if (!array_key_exists($index, $positional)) {
                if ($parameter->isDefaultValueAvailable()) {
                    $arguments[] = $parameter->getDefaultValue();
                    continue;
                }

                throw new \InvalidArgumentException(
                    'Missing route argument for parameter ' . $parameter->getName(),
                );
            }

            $value = $positional[$index];
            $type = $parameter->getType();

            if ($type instanceof ReflectionNamedType && $type->isBuiltin()) {
                $arguments[] = $this->castRouteParam($type->getName(), $value);
                continue;
            }

            $arguments[] = $value;
        }

        $result = $reflection->invoke($controller, ...$arguments);

        if (is_string($result)) {
            echo $result;
        }
    }

    private function castRouteParam(string $typeName, mixed $value): int|float|bool|string {
        if ($typeName === 'int') {
            return (int) $value;
        }
        if ($typeName === 'float') {
            return (float) $value;
        }
        if ($typeName === 'bool') {
            return filter_var($value, FILTER_VALIDATE_BOOLEAN);
        }
        return (string) $value;
    }
}
