<?php
declare(strict_types=1);

namespace Core\Container;

final class Container {

    private array $instances = [];

    public function instance(string $class, object $instance): void {
        $this->instances[$class] = $instance;
    }

    public function get(string $id): object {
        if (isset($this->instances[$id])) {
            return $this->instances[$id];
        }

        throw new \RuntimeException('Container: not registered: ' . $id);
    }
}
