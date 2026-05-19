<?php
declare(strict_types=1);

namespace Core\Database;

use PDO;

final class Connection
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
}
