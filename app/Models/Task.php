<?php
declare(strict_types=1);

namespace App\Models;

use App\Contracts\TaskRepositoryContract;
use Core\Database\Model;

class Task extends Model implements TaskRepositoryContract
{
    protected static string $table = 'tasks';

    public function allTasks(): array
    {
        return $this->findAll();
    }

    public function findTask(int $id): ?array
    {
        return $this->findById($id);
    }

    public function createTask(array $data): int
    {
        return $this->query()->insert($data);
    }

    public function updateTask(int $id, array $data): bool
    {
        return $this
            ->query()
            ->where('id', $id)
            ->update($data);
    }

    public function deleteTask(int $id): bool
    {
        return $this
            ->query()
            ->where('id', $id)
            ->delete();
    }

    public function completeTask(int $id): bool
    {
        return $this->updateTask($id, [
            'status' => 'completed'
        ]);
    }
}
