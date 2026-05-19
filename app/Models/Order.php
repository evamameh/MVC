<?php
declare(strict_types=1);

namespace App\Models;

use Core\Database\Model;

final class Order extends Model
{
    protected static string $table = 'orders';

    public function findAll(string $orderBy = 'created_at', string $direction = 'DESC'): array
    {
        return parent::findAll($orderBy, $direction);
    }

    public function create(array $data): int|string
    {
        return $this->query()->insert([
            'type' => $data['type'],
            'product_id' => $data['product_id'],
            'quantity' => $data['quantity'],
        ]);
    }

    public function update(int|string $id, array $data): bool
    {
        return $this->query()->where('id', $id)->update([
            'type' => $data['type'],
            'product_id' => $data['product_id'],
            'quantity' => $data['quantity'],
        ]);
    }

    public function createWithInventory(Product $products, string $type, int $productId, int $quantity): void
    {
        $this->transaction(function () use ($products, $type, $productId, $quantity): void {
            $this->create([
                'type' => $type,
                'product_id' => $productId,
                'quantity' => $quantity,
            ]);
            $products->applyInventoryForOrderLine($type, $productId, $quantity, false);
        });
    }

    public function updateWithInventory(
        Product $products,
        int|string $id,
        array $existingBefore,
        string $type,
        int $productId,
        int $quantity,
    ): void {
        $this->transaction(function () use ($products, $id, $existingBefore, $type, $productId, $quantity): void {
            $products->applyInventoryForOrderLine(
                (string) ($existingBefore['type'] ?? ''),
                $existingBefore['product_id'],
                (int) ($existingBefore['quantity'] ?? 0),
                true,
            );
            $products->applyInventoryForOrderLine($type, $productId, $quantity, false);

            if (!$this->update($id, [
                'type' => $type,
                'product_id' => $productId,
                'quantity' => $quantity,
            ])) {
                throw new \RuntimeException('Failed to update order.');
            }
        });
    }

    public function deleteWithInventory(Product $products, int|string $id, array $row): void
    {
        $this->transaction(function () use ($products, $id, $row): void {
            $products->applyInventoryForOrderLine(
                (string) ($row['type'] ?? ''),
                $row['product_id'],
                (int) ($row['quantity'] ?? 0),
                true,
            );
            if (!$this->delete($id)) {
                throw new \RuntimeException('Order not found.');
            }
        });
    }
}
