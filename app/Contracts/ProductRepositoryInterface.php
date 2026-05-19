<?php
declare(strict_types=1);

namespace App\Contracts;

interface ProductRepositoryInterface
{
    public function findAll(): array;

    public function findById(int|string $id): ?array;

    
    public function findByNameCategorySupplier(string $name, int|string $categoryId, int|string $supplierId): ?array;

    public function create(array $data): int|string;

    public function update(int|string $id, array $data): bool;

    public function delete(int|string $id): bool;
}
