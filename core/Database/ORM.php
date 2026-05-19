<?php
declare(strict_types=1);

namespace Core\Database;

use PDO;

final class ORM
{
    private PDO $pdo;

    private function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public static function fromConfig(array $config): self
    {
        $host = (string) $config['host'];
        $db = (string) $config['database'];
        $charset = (string) ($config['charset'] ?? 'utf8mb4');
        $dsn = "mysql:host={$host};dbname={$db};charset={$charset}";

        $pdo = new PDO($dsn, (string) $config['username'], (string) $config['password'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);

        return new self($pdo);
    }

    public function pdo(): PDO
    {
        return $this->pdo;
    }

    public function table(string $table): QueryBuilder
    {
        return (new QueryBuilder($this->pdo))->from($table);
    }

    /**
     * @template T of Model
     * @param class-string<T> $modelClass
     * @return T
     */
    public function model(string $modelClass): Model
    {
        return new $modelClass($this);
    }

    public function transaction(callable $callback): mixed
    {
        $this->pdo->beginTransaction();

        try {
            $result = $callback();
            $this->pdo->commit();

            return $result;
        } catch (\Throwable $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }

            throw $e;
        }
    }
}
