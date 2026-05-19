<?php

declare(strict_types=1);







namespace Core\Container;

use Closure;

final class Container
{
    
    private array $bindings = [];

    
    private array $instances = [];

    
    public function singleton(string $id, Closure $resolver): void
    {
        $this->bindings[$id] = function (self $container) use ($resolver, $id): object {
            if (!isset($this->instances[$id])) {
                $this->instances[$id] = $resolver($container);
            }

            return $this->instances[$id];
        };
    }

    
    public function bind(string $id, Closure $resolver): void
    {
        $this->bindings[$id] = $resolver;
    }

    
    public function get(string $id): object
    {
        if (!isset($this->bindings[$id])) {
            throw new \RuntimeException('Container binding missing for ' . $id);
        }

        return $this->bindings[$id]($this);
    }

    
    public function instance(string $class, object $instance): void
    {
        $this->instances[$class] = $instance;
        $this->bindings[$class] = fn () => $instance;
    }
}
