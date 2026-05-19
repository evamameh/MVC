<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Models\Category;
use Core\View\Engine;

final class CategoryController extends BaseController
{
    private Category $categories;

    public function __construct(Engine $view, Category $categories)
    {
        parent::__construct($view);
        $this->categories = $categories;
    }

    
    public function list(): void
    {
        $this->render('category/list', [
            'categories' => $this->categories->findAll(),
            'success' => $this->getFlash('success'),
            'error' => $this->getFlash('error'),
        ]);
    }

    
    public function add(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim((string) ($_POST['name'] ?? ''));

            if ($this->categoryNameExists($name)) {
                $this->render('category/add', [
                    'errors' => ['Category already exists.'],
                    'old' => ['name' => $name],
                ]);

                return;
            }

            $this->categories->create(['name' => $name]);
            $this->setFlash('success', 'Category added successfully.');
            $this->redirect('/category');

            return;
        }

        $this->render('category/add');
    }

    
    public function edit(string $id): void
    {
        $category = $this->categories->findById($id);
        if ($category === null) {
            http_response_code(404);
            echo 'Category not found';

            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim((string) ($_POST['name'] ?? ''));

            if ($this->categoryNameExists($name, (string) $id)) {
                $this->render('category/edit', [
                    'errors' => ['Category already exists.'],
                    'category' => ['id' => $id, 'name' => $name],
                ]);

                return;
            }

            $this->categories->update($id, ['name' => $name]);
            $this->redirect('/category');

            return;
        }

        $this->render('category/edit', ['category' => $category]);
    }

    
    public function delete(string $id): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->categories->delete($id);
            $this->setFlash('success', 'Category deleted successfully.');
            $this->redirect('/category');
        } else {
            $this->redirect('/category');
        }
    }

    
    private function categoryNameExists(string $name, ?string $excludeId = null): bool
    {
        $needle = mb_strtolower(trim($name));
        if ($needle === '') {
            return false;
        }

        foreach ($this->categories->findAll() as $row) {
            $rowId = (string) ($row['id'] ?? '');
            if ($excludeId !== null && $rowId === $excludeId) {
                continue;
            }

            $rowName = mb_strtolower(trim((string) ($row['name'] ?? '')));
            if ($rowName === $needle) {
                return true;
            }
        }

        return false;
    }
}
