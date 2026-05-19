<?php
declare(strict_types=1);






namespace App\Controllers;

final class DashboardController extends BaseController
{
    
    public function home(): never
    {
        $this->redirect('/dashboard');
    }

    
    public function dashboard(): void
    {
        $success = $this->getFlash('success');
        $this->render('dashboard', ['success' => $success]);
    }
}
