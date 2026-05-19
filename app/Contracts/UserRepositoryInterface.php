<?php
declare(strict_types=1);

namespace App\Contracts;

interface UserRepositoryInterface
{
    
    public function findByUsername(string $username): ?array;

    public function findById(int|string $id): ?array;

    
    public function findAllWithoutPasswords(): array;

    public function create(array $data): bool;

    public function update(int|string $id, array $newData): bool;

    public function delete(int|string $id): bool;
}
