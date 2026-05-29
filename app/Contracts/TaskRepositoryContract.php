<?php
declare(strict_types=1);

namespace App\Contracts;

interface TaskRepositoryContract
{
    public function allTasks(): array;

    public function findTask(int $id): ?array;

    public function createTask(array $data): int;

    public function updateTask(int $id, array $data): bool;

    public function deleteTask(int $id): bool;

    public function completeTask(int $id): bool;
}
