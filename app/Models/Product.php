<?php
declare(strict_types=1);









namespace App\Models;

use App\Contracts\ProductRepositoryInterface;
use Core\Database\Model;
use PDO;

final class Product extends Model implements ProductRepositoryInterface
{
    public function findAll(): array
    {
        return $this->query()->from('products')->orderBy('id', 'DESC')->get();
    }

    public function findById(int|string $id): ?array
    {
        return $this->query()->from('products')->where('id', $id)->first();
    }

    
    public function findByNameCategorySupplier(string $name, int|string $categoryId, int|string $supplierId): ?array
    {
        $name = trim($name);
        if ($name === '') {
            return null;
        }

        $stmt = $this->pdo()->prepare(
            <<<'SQL'
                SELECT * FROM products
                WHERE TRIM(name) = :name AND category_id = :category_id AND supplier_id = :supplier_id
                ORDER BY id ASC
                LIMIT 1
                SQL,
        );
        $stmt->execute([
            'name' => $name,
            'category_id' => $categoryId,
            'supplier_id' => $supplierId,
        ]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row === false ? null : $row;
    }

    public function create(array $data): int|string
    {
        if (!$this->existsInTable('categories', $data['category_id'])) {
            throw new \InvalidArgumentException('Invalid category_id: ' . $data['category_id']);
        }
        if (!$this->existsInTable('suppliers', $data['supplier_id'])) {
            throw new \InvalidArgumentException('Invalid supplier_id: ' . $data['supplier_id']);
        }

        return $this->query()->from('products')->insert([
            'name' => $data['name'],
            'category_id' => $data['category_id'],
            'supplier_id' => $data['supplier_id'],
            'quantity' => $data['quantity'],
            'price' => $data['price'] ?? 0,
        ]);
    }

    public function update(int|string $id, array $data): bool
    {
        unset($data['id']);

        return $this->query()->from('products')->where('id', $id)->update([
            'name' => $data['name'],
            'category_id' => $data['category_id'],
            'supplier_id' => $data['supplier_id'],
            'quantity' => $data['quantity'],
            'price' => $data['price'],
        ]);
    }

    
    public function applyInventoryForOrderLine(string $type, int|string $productId, int $quantity, bool $reverse): void
    {
        if ($quantity <= 0) {
            return;
        }

        $t = strtolower(trim($type));
        $delta = match ($t) {
            'sale' => -$quantity,
            'purchase' => $quantity,
            default => throw new \InvalidArgumentException('Unknown order type: ' . $type),
        };

        if ($reverse) {
            $delta = -$delta;
        }

        if ($delta < 0) {
            $take = -$delta;
            $stmt = $this->pdo()->prepare(
                'UPDATE products SET quantity = quantity - :take WHERE id = :id AND quantity >= :need',
            );
            $stmt->execute(['take' => $take, 'need' => $take, 'id' => $productId]);
        } else {
            $stmt = $this->pdo()->prepare(
                'UPDATE products SET quantity = quantity + :add WHERE id = :id',
            );
            $stmt->execute(['add' => $delta, 'id' => $productId]);
        }

        if ($stmt->rowCount() < 1) {
            throw new \RuntimeException(
                $delta < 0
                    ? 'Insufficient product quantity for this order (not enough stock).'
                    : 'Inventory update failed (product not found).',
            );
        }
    }

    
    public function delete(int|string $id): bool
    {
        $this->pdo()->beginTransaction();
        try {
            $this->query()->from('orders')->where('product_id', $id)->delete();

            $ok = $this->query()->from('products')->where('id', $id)->delete();

            $this->pdo()->commit();

            return $ok;
        } catch (\Throwable $e) {
            if ($this->pdo()->inTransaction()) {
                $this->pdo()->rollBack();
            }
            throw $e;
        }
    }

    private function existsInTable(string $table, int|string $id): bool
    {
        $allowed = ['categories', 'suppliers'];
        if (!in_array($table, $allowed, true)) {
            return false;
        }

        return $this->query()->from($table)->where('id', $id)->count() > 0;
    }
}
