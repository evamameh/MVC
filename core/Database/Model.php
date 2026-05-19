<?php
declare(strict_types=1);

namespace Core\Database;

use PDO;

/**
 * Base ORM model — each subclass maps one database table (object ↔ relation).
 */
abstract class Model
{
    protected static string $table = '';

    protected ORM $orm;

    public function __construct(ORM $orm)
    {
        $this->orm = $orm;
    }

    public static function tableName(): string
    {
        if (static::$table === '') {
            throw new \RuntimeException(static::class . ' must define protected static string $table');
        }

        return static::$table;
    }

    protected function pdo(): PDO
    {
        return $this->orm->pdo();
    }

    protected function query(): QueryBuilder
    {
        return $this->orm->table(static::tableName());
    }

    protected function table(string $name): QueryBuilder
    {
        return $this->orm->table($name);
    }

    public function transaction(callable $callback): mixed
    {
        return $this->orm->transaction($callback);
    }

    public function findAll(string $orderBy = 'id', string $direction = 'DESC'): array
    {
        return $this->query()->orderBy($orderBy, $direction)->get();
    }

    public function findById(int|string $id): ?array
    {
        return $this->query()->where('id', $id)->first();
    }

    public function delete(int|string $id): bool
    {
        return $this->query()->where('id', $id)->delete();
    }

    protected function relatedExists(string $table, int|string $id): bool
    {
        return $this->table($table)->where('id', $id)->count() > 0;
    }
}
