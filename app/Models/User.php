<?php
declare(strict_types=1);

namespace App\Models;

use App\Contracts\UserRepositoryInterface;
use Core\Database\Model;

final class User extends Model implements UserRepositoryInterface
{
    public function findByUsername(string $username): ?array
    {
        return $this->query()->from('users')->where('username', $username)->first();
    }

    public function findById(int|string $id): ?array
    {
        return $this->query()->from('users')->where('id', $id)->first();
    }

    
    public function findAllWithoutPasswords(): array
    {
        return $this->query()->from('users')->select('id', 'username')->orderBy('id', 'DESC')->get();
    }

    public function create(array $data): bool
    {
        $this->query()->from('users')->insert([
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

        return $this->query()->from('users')->where('id', $id)->update($allowed);
    }

    public function delete(int|string $id): bool
    {
        return $this->query()->from('users')->where('id', $id)->delete();
    }
}
