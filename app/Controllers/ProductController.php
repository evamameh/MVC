<?php
declare(strict_types=1);









namespace App\Controllers;

use App\Contracts\ProductRepositoryInterface;
use App\Models\Category;
use App\Models\Supplier;
use Core\View\Engine;

final class ProductController extends BaseController
{
    private ProductRepositoryInterface $products;
    private Category $categories;
    private Supplier $suppliers;

    public function __construct(
        Engine $view,
        ProductRepositoryInterface $products,
        Category $categories,
        Supplier $suppliers,
    ) {
        parent::__construct($view);
        $this->products = $products;
        $this->categories = $categories;
        $this->suppliers = $suppliers;
    }

    
    public function list(): void
    {
        $categoryNameById = [];
        foreach ($this->categories->findAll() as $c) {
            $categoryNameById[(string) ($c['id'] ?? '')] = (string) ($c['name'] ?? '');
        }

        $products = $this->products->findAll();
        usort($products, static function (array $a, array $b): int {
            return strcasecmp((string) ($a['name'] ?? ''), (string) ($b['name'] ?? ''));
        });

        $this->render('product/list', [
            'products' => $products,
            'categoryNameById' => $categoryNameById,
            'success' => $this->getFlash('success'),
            'error' => $this->getFlash('error'),
        ]);
    }

    
    public function add(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->render('product/add', $this->formData());

            return;
        }

        $categoryId = (string) ($_POST['category_id'] ?? '');
        $supplierId = (string) ($_POST['supplier_id'] ?? '');
        $name = trim((string) ($_POST['name'] ?? ''));
        $quantity = (int) ($_POST['quantity'] ?? 0);
        $price = (float) ($_POST['price'] ?? 0);

        try {
            $existing = $this->products->findByNameCategorySupplier($name, $categoryId, $supplierId);
            if ($existing !== null) {
                
                $newQty = (int) ($existing['quantity'] ?? 0) + $quantity;
                $this->products->update($existing['id'], [
                    'name' => $name,
                    'category_id' => $existing['category_id'],
                    'supplier_id' => $existing['supplier_id'],
                    'quantity' => $newQty,
                    'price' => (float) ($existing['price'] ?? $price),
                ]);
                $this->setFlash('success', 'Quantity added to existing product.');
            } else {
                $this->products->create([
                    'name' => $name,
                    'category_id' => $categoryId,
                    'supplier_id' => $supplierId,
                    'quantity' => $quantity,
                    'price' => $price,
                ]);
                $this->setFlash('success', 'Product added successfully.');
            }
            $this->redirect('/products');
        } catch (\Throwable $e) {
            $this->render('product/add', $this->formData(['error' => $e->getMessage()]));
        }
    }

    
    public function edit(string $id): void
    {
        $product = $this->products->findById($id);
        if ($product === null) {
            http_response_code(404);
            echo 'Product not found!';

            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $product = [
                'id' => $id,
                'name' => trim((string) ($_POST['name'] ?? '')),
                'category_id' => $_POST['category_id'] ?? '',
                'supplier_id' => $_POST['supplier_id'] ?? '',
                'quantity' => (int) ($_POST['quantity'] ?? 0),
                'price' => (float) ($_POST['price'] ?? 0),
            ];

            try {
                $this->products->update($id, $product);
                $this->redirect('/products');
            } catch (\Throwable $e) {
                $this->render('product/edit', $this->formData([
                    'product' => $product,
                    'error' => $e->getMessage(),
                ]));
            }

            return;
        }

        $this->render('product/edit', $this->formData(['product' => $product]));
    }

    
    public function delete(string $id): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/products');

            return;
        }

        $row = $this->products->findById($id);
        if ($row === null) {
            $this->setFlash('error', 'Product not found.');
            $this->redirect('/products');

            return;
        }

        try {
            if ($this->products->delete($id)) {
                $this->setFlash('success', 'Product deleted successfully.');
            } else {
                $this->setFlash('error', 'Failed to delete product.');
            }
        } catch (\Throwable $e) {
            $this->setFlash('error', 'Failed to delete product: ' . $e->getMessage());
        }

        $this->redirect('/products');
    }

    
    private function formData(array $extra = []): array
    {
        return array_merge([
            'categories' => $this->categories->findAll(),
            'suppliers' => $this->suppliers->findAll(),
        ], $extra);
    }
}
