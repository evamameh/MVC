<?php
declare(strict_types=1);











namespace App\Controllers;

use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use Core\Database\Connection;
use Core\View\Engine;

final class OrderController extends BaseController
{
    private const ORDER_TYPES = ['purchase', 'sale', 'return'];
    private const ORDER_TYPES_ADD = ['purchase', 'sale']; 

    private Order $orders;
    private Product $products;
    private Category $categories;
    private Connection $connection;

    public function __construct(
        Engine $view,
        Order $orders,
        Product $products,
        Category $categories,
        Connection $connection,
    ) {
        parent::__construct($view);
        $this->orders = $orders;
        $this->products = $products;
        $this->categories = $categories;
        $this->connection = $connection;
    }

    
    private static function inventoryDeltaWhenApplied(string $type, int $quantity): int
    {
        return match (strtolower(trim($type))) {
            'sale' => -$quantity,
            'purchase', 'return' => $quantity,
            default => 0,
        };
    }

    
    private function stockAvailableForNewSale(array $existingOrder, int $productId): int
    {
        $row = $this->products->findById($productId);
        $stock = (int) ($row['quantity'] ?? 0);
        if ((string) ($existingOrder['product_id'] ?? '') !== (string) $productId) {
            return $stock;
        }

        return $stock - self::inventoryDeltaWhenApplied((string) ($existingOrder['type'] ?? ''), (int) ($existingOrder['quantity'] ?? 0));
    }

    
    private function withTransaction(callable $callback): mixed
    {
        $pdo = $this->connection->pdo();
        $pdo->beginTransaction();

        try {
            $result = $callback();
            $pdo->commit();

            return $result;
        } catch (\Throwable $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            throw $e;
        }
    }

    
    public function list(): void
    {
        $orders = $this->orders->findAll();
        $productMap = [];
        $productPrices = [];
        $productStockById = [];
        foreach ($this->products->findAll() as $product) {
            $pid = (string) ($product['id'] ?? '');
            $productMap[$pid] = (string) ($product['name'] ?? '');
            $productPrices[$pid] = (float) ($product['price'] ?? 0);
            $productStockById[$pid] = (int) ($product['quantity'] ?? 0);
        }

        foreach ($orders as &$order) {
            $pid = (string) ($order['product_id'] ?? '');
            $order['product_name'] = $productMap[$pid] ?? 'Unknown product';
            $qty = (int) ($order['quantity'] ?? 0);
            $unit = $productPrices[$pid] ?? 0.0;
            $order['unit_price'] = $unit;
            $order['line_amount'] = $unit * $qty;
            $order['product_qty'] = $productStockById[$pid] ?? 0;
        }
        unset($order);

        $this->render('order/list', [
            'orders' => $orders,
            'success' => $this->getFlash('success'),
            'error' => $this->getFlash('error'),
        ]);
    }

    
    public function add(): void
    {
        $viewData = [
            'products' => $this->products->findAll(),
            'categories' => $this->categories->findAll(),
        ];

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->render('order/add', $viewData);

            return;
        }

        $type = (string) ($_POST['type'] ?? '');
        $productId = (int) ($_POST['product_id'] ?? 0);
        $quantity = (int) ($_POST['quantity'] ?? 0);
        $errors = [];

        if ($type === 'sale') {
            $p = $this->products->findById($productId);
            if ($p === null || (int) ($p['quantity'] ?? 0) < $quantity) {
                $errors[] = 'Not enough stock for this sale.';
            }
        }

        if ($errors === []) {
            try {
                $this->withTransaction(function () use ($type, $productId, $quantity): void {
                    $this->orders->create([
                        'type' => $type,
                        'product_id' => $productId,
                        'quantity' => $quantity,
                    ]);
                    $this->products->applyInventoryForOrderLine($type, $productId, $quantity, false);
                });
                $this->setFlash('success', 'Order saved successfully.');
                $this->redirect('/order');

                return;
            } catch (\Throwable $e) {
                $errors[] = $e->getMessage();
            }
        }

        $this->render('order/add', array_merge($viewData, [
            'errors' => $errors,
            'old' => $_POST,
        ]));
    }

    
    public function edit(string $id): void
    {
        $order = $this->orders->findById($id);
        if ($order === null) {
            http_response_code(404);
            echo 'Order not found';

            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $errors = [];
            $type = (string) ($_POST['type'] ?? '');
            $productId = $_POST['product_id'] ?? '';
            $quantity = $_POST['quantity'] ?? '';

            if (!in_array($type, self::ORDER_TYPES, true)) {
                $errors[] = 'Invalid order type selected.';
            }

            if (empty($productId) || !ctype_digit((string) $productId) || (int) $productId <= 0) {
                $errors[] = 'Please select a valid product.';
            } else {
                $productId = (int) $productId;
                if ($this->products->findById($productId) === null) {
                    $errors[] = 'Selected product does not exist.';
                }
            }

            if (empty($quantity) || !ctype_digit((string) $quantity) || (int) $quantity <= 0) {
                $errors[] = 'Please enter a valid quantity greater than 0.';
            } else {
                $quantity = (int) $quantity;
            }

            $existingBefore = null;
            if ($errors === []) {
                $existingBefore = $this->orders->findById($id);
                if ($existingBefore === null) {
                    $errors[] = 'Order not found.';
                } elseif ($type === 'sale' && $this->stockAvailableForNewSale($existingBefore, $productId) < $quantity) {
                    $errors[] = 'Not enough stock for this sale.';
                }
            }

            if ($errors === []) {
                try {
                    assert($existingBefore !== null);

                    $this->withTransaction(function () use ($id, $existingBefore, $type, $productId, $quantity): void {
                        
                        $this->products->applyInventoryForOrderLine(
                            (string) ($existingBefore['type'] ?? ''),
                            $existingBefore['product_id'],
                            (int) ($existingBefore['quantity'] ?? 0),
                            true,
                        );
                        
                        $this->products->applyInventoryForOrderLine($type, $productId, $quantity, false);

                        $updated = $this->orders->update($id, [
                            'type' => $type,
                            'product_id' => $productId,
                            'quantity' => $quantity,
                        ]);

                        if (!$updated) {
                            throw new \RuntimeException('Failed to update order.');
                        }
                    });

                    $this->setFlash('success', 'Order updated successfully.');
                    $this->redirect('/order');

                    return;
                } catch (\Throwable $e) {
                    $errors[] = $e->getMessage();
                }
            }

            $this->render('order/edit', [
                'order' => [
                    'id' => $id,
                    'type' => $type,
                    'product_id' => $productId,
                    'quantity' => $quantity,
                ],
                'products' => $this->products->findAll(),
                'categories' => $this->categories->findAll(),
                'errors' => $errors,
            ]);

            return;
        }

        $this->render('order/edit', [
            'order' => $order,
            'products' => $this->products->findAll(),
            'categories' => $this->categories->findAll(),
        ]);
    }

    
    public function delete(string $id): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/order');

            return;
        }

        try {
            $row = $this->orders->findById($id);
            if ($row === null) {
                $this->setFlash('error', 'Order not found.');
            } else {
                $this->withTransaction(function () use ($row, $id): void {
                    $this->products->applyInventoryForOrderLine(
                        (string) ($row['type'] ?? ''),
                        $row['product_id'],
                        (int) ($row['quantity'] ?? 0),
                        true,
                    );
                    if (!$this->orders->delete($id)) {
                        throw new \RuntimeException('Order not found.');
                    }
                });
                $this->setFlash('success', 'Order deleted successfully.');
            }
        } catch (\Throwable $e) {
            $this->setFlash('error', 'Could not delete order: ' . $e->getMessage());
        }

        $this->redirect('/order');
    }
}
