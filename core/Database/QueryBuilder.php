<?php
declare(strict_types=1);

namespace Core\Database;

use PDO;

final class QueryBuilder
{
    private string $table = '';           
    private array $wheres = [];           
    private string $orderColumn = '';
    private string $orderDirection = 'ASC';
    private array $columns = ['*'];       
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function from(string $table): self
    {
        $copy = clone $this;
        $copy->table = $table;
        $copy->wheres = [];
        $copy->orderColumn = '';
        $copy->columns = ['*'];

        return $copy;
    }

    public function select(string ...$columns): self
    {
        $copy = clone $this;
        $copy->columns = $columns === [] ? ['*'] : $columns;

        return $copy;
    }

    public function where(string $column, mixed $value): self
    {
        $copy = clone $this;
        $copy->wheres[$column] = $value;

        return $copy;
    }
    
    public function orderBy(string $column, string $direction = 'ASC'): self
    {
        $copy = clone $this;
        $copy->orderColumn = $column;
        $copy->orderDirection = strtoupper($direction) === 'DESC' ? 'DESC' : 'ASC';

        return $copy;
    }
    
    public function get(): array
    {
        $sql = $this->buildSelectSql();
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($this->wheres);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function first(): ?array
    {
        $sql = $this->buildSelectSql() . ' LIMIT 1';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($this->wheres);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row === false ? null : $row;
    }

    public function count(): int
    {
        $sql = "SELECT COUNT(*) FROM {$this->table}";
        $params = [];
        if ($this->wheres !== []) {
            $parts = [];
            foreach ($this->wheres as $col => $val) {
                $parts[] = "{$col} = :{$col}";
                $params[$col] = $val;
            }
            $sql .= ' WHERE ' . join(' AND ', $parts);
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return (int) $stmt->fetchColumn();
    }

    public function insert(array $data): int|string
    {
        $cols = array_keys($data);
        $placeholders = array_map(static fn (string $c): string => ':' . $c, $cols);
        $sql = sprintf(
            'INSERT INTO %s (%s) VALUES (%s)',
            $this->table,
            join(', ', $cols),
            join(', ', $placeholders),
        );

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($data);

        return $this->pdo->lastInsertId();
    }
    
    public function update(array $data): bool
    {
        if ($this->wheres === []) {
            return false;
        }

        $setParts = [];
        foreach (array_keys($data) as $col) {
            $setParts[] = "{$col} = :set_{$col}";
        }
        $whereParts = [];
        foreach (array_keys($this->wheres) as $col) {
            $whereParts[] = "{$col} = :where_{$col}";
        }

        $sql = sprintf(
            'UPDATE %s SET %s WHERE %s',
            $this->table,
            join(', ', $setParts),
            join(' AND ', $whereParts),
        );

        $params = [];
        foreach ($data as $col => $val) {
            $params['set_' . $col] = $val;
        }
        foreach ($this->wheres as $col => $val) {
            $params['where_' . $col] = $val;
        }

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute($params);
    }

    public function delete(): bool
    {
        if ($this->wheres === []) {
            return false;
        }

        $parts = [];
        foreach (array_keys($this->wheres) as $col) {
            $parts[] = "{$col} = :{$col}";
        }
        $sql = sprintf('DELETE FROM %s WHERE %s', $this->table, join(' AND ', $parts));
        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute($this->wheres);
    }

    private function buildSelectSql(): string
    {
        if ($this->table === '') {
            throw new \RuntimeException('QueryBuilder: table not set. Call from("table_name") first.');
        }

        $sql = 'SELECT ' . join(', ', $this->columns) . ' FROM ' . $this->table;

        if ($this->wheres !== []) {
            $parts = [];
            foreach (array_keys($this->wheres) as $col) {
                $parts[] = "{$col} = :{$col}";
            }
            $sql .= ' WHERE ' . join(' AND ', $parts);
        }

        if ($this->orderColumn !== '') {
            $sql .= ' ORDER BY ' . $this->orderColumn . ' ' . $this->orderDirection;
        }

        return $sql;
    }
}
