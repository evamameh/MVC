<?php
declare(strict_types=1);

namespace App\Models;

use Core\Database\Model;

final class Category extends Model
{
    public function findAll(): array
    {
        return $this->query()->from('categories')->orderBy('id', 'DESC')->get();
    }

    public function findById(int|string $id): ?array
    {
        return $this->query()->from('categories')->where('id', $id)->first();
    }

    public function create(array $data): int|string
    {
        return $this->query()->from('categories')->insert(['name' => $data['name']]);
    }

    public function update(int|string $id, array $data): bool
    {
        return $this->query()->from('categories')->where('id', $id)->update(['name' => $data['name']]);
    }

    public function delete(int|string $id): bool
    {
        $this->pdo()->beginTransaction();
        try {
            
            $this->pdo()->prepare(
                'DELETE o FROM orders o INNER JOIN products p ON o.product_id = p.id WHERE p.category_id = :id',
            )->execute(['id' => $id]);

            $this->query()->from('products')->where('category_id', $id)->delete();
            $ok = $this->query()->from('categories')->where('id', $id)->delete();

            $this->pdo()->commit();

            return $ok;
        } catch (\Throwable $e) {
            if ($this->pdo()->inTransaction()) {
                $this->pdo()->rollBack();
            }
            throw $e;
        }
    }
}
