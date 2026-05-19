<?php
declare(strict_types=1);

namespace App\Models;

use Core\Database\Model;

final class Supplier extends Model
{
    protected static string $table = 'suppliers';

    public function create(array $data): int|string
    {
        return $this->query()->insert([
            'name' => $data['name'],
            'contact' => $data['contact'],
        ]);
    }

    public function update(int|string $id, array $data): bool
    {
        return $this->query()->where('id', $id)->update([
            'name' => $data['name'],
            'contact' => $data['contact'],
        ]);
    }
}
