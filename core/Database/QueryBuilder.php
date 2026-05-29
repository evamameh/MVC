<?php
declare(strict_types=1);

namespace Core\Database;

use PDO;

final class QueryBuilder
{
    private array $wheres = [];

    private string $orderBy = '';

    public function __construct(
        private PDO $pdo,
        private string $table
    ) {
        $this->checkName($table);
    }

    public function where(string $column, string|int|float|null $value): self
    {
        $this->checkName($column);
        $this->wheres[$column] = $value;

        return $this;
    }

    public function orderBy(string $column, string $direction = 'ASC'): self
    {
        $this->checkName($column);
        $direction = strtoupper($direction) === 'DESC' ? 'DESC' : 'ASC';
        $this->orderBy = "{$column} {$direction}";

        return $this;
    }

    public function get(): array
    {
        $stmt = $this->pdo->prepare($this->selectSql());
        $stmt->execute($this->wheres);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function first(): ?array
    {
        $stmt = $this->pdo->prepare($this->selectSql() . ' LIMIT 1');
        $stmt->execute($this->wheres);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row === false ? null : $row;
    }

    public function insert(array $data): int
    {
        $columns = $this->columns($data);
        $placeholders = array_map(fn (string $column): string => ':' . $column, $columns);

        $sql = sprintf(
            'INSERT INTO %s (%s) VALUES (%s)',
            $this->table,
            implode(', ', $columns),
            implode(', ', $placeholders)
        );

        $this->pdo->prepare($sql)->execute($data);

        return (int) $this->pdo->lastInsertId();
    }

    public function update(array $data): bool
    {
        if ($this->wheres === []) {
            return false;
        }

        $columns = $this->columns($data);
        $set = array_map(fn (string $column): string => "{$column} = :set_{$column}", $columns);
        $where = $this->whereSql('where_');

        $params = [];
        foreach ($data as $column => $value) {
            $params['set_' . $column] = $value;
        }
        foreach ($this->wheres as $column => $value) {
            $params['where_' . $column] = $value;
        }

        $sql = sprintf(
            'UPDATE %s SET %s WHERE %s',
            $this->table,
            implode(', ', $set),
            $where
        );

        return $this->pdo->prepare($sql)->execute($params);
    }

    public function delete(): bool
    {
        if ($this->wheres === []) {
            return false;
        }

        $sql = sprintf(
            'DELETE FROM %s WHERE %s',
            $this->table,
            $this->whereSql()
        );

        return $this->pdo->prepare($sql)->execute($this->wheres);
    }

    private function selectSql(): string
    {
        $sql = 'SELECT * FROM ' . $this->table;

        if ($this->wheres !== []) {
            $sql .= ' WHERE ' . $this->whereSql();
        }

        if ($this->orderBy !== '') {
            $sql .= ' ORDER BY ' . $this->orderBy;
        }

        return $sql;
    }

    private function whereSql(string $prefix = ''): string
    {
        $parts = [];

        foreach (array_keys($this->wheres) as $column) {
            $parts[] = "{$column} = :{$prefix}{$column}";
        }

        return implode(' AND ', $parts);
    }

    private function columns(array $data): array
    {
        $columns = array_map('strval', array_keys($data));

        foreach ($columns as $column) {
            $this->checkName($column);
        }

        return $columns;
    }

    private function checkName(string $name): void
    {
        if (preg_match('/^[A-Za-z_][A-Za-z0-9_]*$/', $name) !== 1) {
            throw new \InvalidArgumentException('Invalid SQL name: ' . $name);
        }
    }
}
