<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Contracts\UserRepositoryInterface;
use Core\View\Engine;

final class RegisterController extends BaseController
{
    private UserRepositoryInterface $users;

    public function __construct(Engine $view, UserRepositoryInterface $users)
    {
        parent::__construct($view);
        $this->users = $users;
    }

    
    public function showRegisterForm(): void
    {
        $this->render('register');
    }

    
    public function register(): void
    {
        $username = trim((string) ($_POST['username'] ?? ''));
        $password = (string) ($_POST['password'] ?? '');

        if ($this->users->findByUsername($username) !== null) {
            $this->render('register', [
                'error' => 'Username already taken.',
                'username' => $username,
            ]);

            return;
        }

        $this->users->create([
            'username' => $username,
            'password' => password_hash($password, PASSWORD_BCRYPT),
        ]);

        $this->setFlash('success', 'Account created. Please sign in.');
        $this->redirect('/login');
    }
}
