<?php

declare(strict_types=1);






namespace Core\Database;

use PDO;

abstract class Model
{
    protected Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    protected function pdo(): PDO
    {
        return $this->connection->pdo();
    }

    
    protected function query(): QueryBuilder
    {
        return new QueryBuilder($this->pdo());
    }
}
