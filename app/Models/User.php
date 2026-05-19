<?php
declare(strict_types=1);

namespace App\Models;

use App\Contracts\UserRepositoryInterface;
use Core\Database\Model;

final class User extends Model implements UserRepositoryInterface
{
    protected static string $table = 'users';

    public function findByUsername(string $username): ?array
    {
        return $this->query()->where('username', $username)->first();
    }

    public function findAllWithoutPasswords(): array
    {
        return $this->query()->select('id', 'username')->orderBy('id', 'DESC')->get();
    }

    public function create(array $data): bool
    {
        $this->query()->insert([
            'username' => $data['username'],
            'password' => $data['password'],
        ]);

        return true;
    }

    public function update(int|string $id, array $newData): bool
    {
        $allowed = [];
        foreach (['username', 'password'] as $key) {
            if (array_key_exists($key, $newData)) {
                $allowed[$key] = $newData[$key];
            }
        }
        if ($allowed === []) {
            return false;
        }

        return $this->query()->where('id', $id)->update($allowed);
    }
}
