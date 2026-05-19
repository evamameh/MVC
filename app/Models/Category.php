<?php
declare(strict_types=1);

namespace App\Models;

use Core\Database\Model;

final class Category extends Model
{
    protected static string $table = 'categories';

    public function create(array $data): int|string
    {
        return $this->query()->insert(['name' => $data['name']]);
    }

    public function update(int|string $id, array $data): bool
    {
        return $this->query()->where('id', $id)->update(['name' => $data['name']]);
    }

    public function delete(int|string $id): bool
    {
        return $this->transaction(function () use ($id): bool {
            $this->pdo()->prepare(
                'DELETE o FROM orders o INNER JOIN products p ON o.product_id = p.id WHERE p.category_id = :id',
            )->execute(['id' => $id]);

            $this->table('products')->where('category_id', $id)->delete();

            return $this->query()->where('id', $id)->delete();
        });
    }
}
