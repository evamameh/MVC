<?php
declare(strict_types=1);

namespace Core\Database;

abstract class Model {

    protected static string $table = '';

    protected Connection $connection;

    public function __construct(Connection $connection) {
        $this->connection = $connection;
    }

    public static function tableName(): string {
        if (static::$table === '') {
            throw new \RuntimeException(static::class . ' must define protected static string $table');
        }

        return static::$table;
    }

    protected function query(): QueryBuilder {
        return $this->connection->query(static::tableName());
    }

    public function findAll(string $orderBy = 'id', string $direction = 'ASC'): array {
        return $this->query()->orderBy($orderBy, $direction)->get();
    }

    public function findById(int $id): ?array {
        return $this->query()->where('id', $id)->first();
    }
}
