<?php
declare(strict_types=1);







namespace App\Controllers;

use App\Contracts\UserRepositoryInterface;
use Core\View\Engine;

final class LoginController extends BaseController
{
    private UserRepositoryInterface $users;

    public function __construct(Engine $view, UserRepositoryInterface $users)
    {
        parent::__construct($view);
        $this->users = $users;
    }

    
    public function showLoginForm(): void
    {
        $success = $this->getFlash('success');
        $this->render('login', $success !== null ? ['success' => $success] : []);
    }

    
    public function login(): void
    {
        $username = trim((string) ($_POST['username'] ?? ''));
        $password = (string) ($_POST['password'] ?? '');

        $user = $this->users->findByUsername($username);
        if ($user !== null && password_verify($password, (string) ($user['password'] ?? ''))) {
            $_SESSION['user_id'] = $user['id'];       
            $_SESSION['username'] = $user['username'];
            $this->setFlash('success', 'Login successful.');
            $this->redirect('/dashboard');

            return;
        }

        $this->render('login', ['error' => 'Invalid username or password.']);
    }

    
    public function logout(): void
    {
        session_destroy();
        $this->redirect('/login');
    }
}
