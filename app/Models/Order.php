<?php
declare(strict_types=1);






namespace App\Models;

use Core\Database\Model;

final class Order extends Model
{
    public function findAll(): array
    {
        return $this->pdo()->query('SELECT * FROM orders ORDER BY created_at DESC, id DESC')->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function findById(int|string $id): ?array
    {
        return $this->query()->from('orders')->where('id', $id)->first();
    }

    public function create(array $data): int|string
    {
        return $this->query()->from('orders')->insert([
            'type' => $data['type'],
            'product_id' => $data['product_id'],
            'quantity' => $data['quantity'],
        ]);
    }

    public function update(int|string $id, array $data): bool
    {
        return $this->query()->from('orders')->where('id', $id)->update([
            'type' => $data['type'],
            'product_id' => $data['product_id'],
            'quantity' => $data['quantity'],
        ]);
    }

    public function delete(int|string $id): bool
    {
        return $this->query()->from('orders')->where('id', $id)->delete();
    }
}
