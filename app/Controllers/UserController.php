<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Contracts\UserRepositoryInterface;
use Core\View\Engine;

final class UserController extends BaseController
{
    private UserRepositoryInterface $users;

    public function __construct(Engine $view, UserRepositoryInterface $users)
    {
        parent::__construct($view);
        $this->users = $users;
    }

    
    public function list(): void
    {
        $all = $this->users->findAllWithoutPasswords();
        $perPage = 10;
        $page = max(1, (int) ($_GET['page'] ?? 1));
        $totalPages = max(1, (int) ceil(count($all) / $perPage));
        if ($page > $totalPages) {
            $page = $totalPages;
        }

        $this->render('user/list', [
            'users' => array_slice($all, ($page - 1) * $perPage, $perPage),
            'totalUsers' => count($all),
            'page' => $page,
            'totalPages' => $totalPages,
            'success' => $this->getFlash('success'),
            'error' => $this->getFlash('error'),
        ]);
    }

    
    public function showCreate(): void
    {
        $this->render('user/create');
    }

    
    public function create(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/userlist/create');

            return;
        }

        $username = trim((string) ($_POST['username'] ?? ''));
        $password = (string) ($_POST['password'] ?? '');

        if ($this->users->findByUsername($username) !== null) {
            $this->render('user/create', ['error' => 'Username already exists.']);

            return;
        }

        $this->users->create([
            'username' => $username,
            'password' => password_hash($password, PASSWORD_BCRYPT),
        ]);

        $this->setFlash('success', 'User created successfully.');
        $this->redirect('/userlist');
    }

    
    public function edit(string $id): void
    {
        $user = $this->users->findById($id);
        if ($user === null) {
            $this->setFlash('error', 'User not found.');
            $this->redirect('/userlist');

            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            unset($user['password']);
            $this->render('user/edit', ['user' => $user]);

            return;
        }

        $username = trim((string) ($_POST['username'] ?? ''));
        unset($user['password']);

        $existing = $this->users->findByUsername($username);
        if ($existing !== null && (string) ($existing['id'] ?? '') !== (string) $id) {
            $user['username'] = $username;
            $this->render('user/edit', ['user' => $user, 'error' => 'Username already exists.']);

            return;
        }

        $update = ['username' => $username];
        $plain = (string) ($_POST['password'] ?? '');
        if ($plain !== '') {
            $update['password'] = password_hash($plain, PASSWORD_BCRYPT);
        }

        $this->users->update($id, $update);
        $this->setFlash('success', 'User updated successfully.');
        $this->redirect('/userlist');
    }

    
    public function delete(string $id): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->users->delete($id);
            $this->setFlash('success', 'User deleted successfully.');
        }

        $this->redirect('/userlist');
    }
}
