<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Models\Supplier;
use Core\View\Engine;

final class SupplierController extends BaseController
{
    private Supplier $suppliers;

    public function __construct(Engine $view, Supplier $suppliers)
    {
        parent::__construct($view);
        $this->suppliers = $suppliers;
    }

    
    public function list(): void
    {
        $this->render('supplier/list', [
            'suppliers' => $this->suppliers->findAll(),
            'success' => $this->getFlash('success'),
            'error' => $this->getFlash('error'),
        ]);
    }

    
    public function add(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim((string) ($_POST['name'] ?? ''));
            $contact = trim((string) ($_POST['contact'] ?? ''));

            try {
                $this->suppliers->create(['name' => $name, 'contact' => $contact]);
                $this->setFlash('success', 'Supplier added successfully.');
                $this->redirect('/supplier');
            } catch (\Throwable $e) {
                $this->render('supplier/add', ['error' => 'Failed to add supplier: ' . $e->getMessage()]);
            }

            return;
        }

        $this->render('supplier/add');
    }

    
    public function edit(string $id): void
    {
        $supplier = $this->suppliers->findById($id);
        if ($supplier === null) {
            http_response_code(404);
            echo 'Supplier not found!';

            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim((string) ($_POST['name'] ?? ''));
            $contact = trim((string) ($_POST['contact'] ?? ''));
            $this->suppliers->update($id, ['name' => $name, 'contact' => $contact]);
            $this->redirect('/supplier');

            return;
        }

        $this->render('supplier/edit', ['supplier' => $supplier]);
    }

    
    public function delete(string $id): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/supplier');
        }

        $this->suppliers->delete($id);
        $this->setFlash('success', 'Supplier deleted successfully.');
        $this->redirect('/supplier');
    }
}
