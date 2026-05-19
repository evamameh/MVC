<?php
declare(strict_types=1);

namespace App\Models;

use Core\Database\Model;

final class Supplier extends Model
{
    public function findAll(): array
    {
        return $this->query()->from('suppliers')->orderBy('id', 'DESC')->get();
    }

    public function findById(int|string $id): ?array
    {
        return $this->query()->from('suppliers')->where('id', $id)->first();
    }

    public function create(array $data): int|string
    {
        return $this->query()->from('suppliers')->insert([
            'name' => $data['name'],
            'contact' => $data['contact'],
        ]);
    }

    public function update(int|string $id, array $data): bool
    {
        return $this->query()->from('suppliers')->where('id', $id)->update([
            'name' => $data['name'],
            'contact' => $data['contact'],
        ]);
    }

    public function delete(int|string $id): bool
    {
        return $this->query()->from('suppliers')->where('id', $id)->delete();
    }
}
