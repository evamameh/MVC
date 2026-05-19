<?php
declare(strict_types=1);

use App\Controllers\CategoryController;
use App\Controllers\LoginController;
use App\Controllers\OrderController;
use App\Controllers\DashboardController;
use App\Controllers\ProductController;
use App\Controllers\RegisterController;
use App\Controllers\SupplierController;
use App\Controllers\UserController;
use App\Middleware\AuthMiddleware;

$auth = [[AuthMiddleware::class, 'requireSession']];

return [
    
    ['method' => 'GET', 'path' => '/', 'controller' => DashboardController::class, 'action' => 'home', 'middleware' => $auth],
    ['method' => 'GET', 'path' => '/dashboard', 'controller' => DashboardController::class, 'action' => 'dashboard', 'middleware' => $auth],

    
    ['method' => 'GET', 'path' => '/login', 'controller' => LoginController::class, 'action' => 'showLoginForm'],
    ['method' => 'POST', 'path' => '/login', 'controller' => LoginController::class, 'action' => 'login'],
    ['method' => 'GET', 'path' => '/register', 'controller' => RegisterController::class, 'action' => 'showRegisterForm'],
    ['method' => 'POST', 'path' => '/register', 'controller' => RegisterController::class, 'action' => 'register'],
    ['method' => 'GET', 'path' => '/logout', 'controller' => LoginController::class, 'action' => 'logout'],

    
    ['method' => 'GET', 'path' => '/userlist', 'controller' => UserController::class, 'action' => 'list', 'middleware' => $auth],
    ['method' => 'GET', 'path' => '/userlist/create', 'controller' => UserController::class, 'action' => 'showCreate', 'middleware' => $auth],
    ['method' => 'POST', 'path' => '/userlist/create', 'controller' => UserController::class, 'action' => 'create', 'middleware' => $auth],
    ['method' => 'GET', 'path' => '/user/edit/{id}', 'controller' => UserController::class, 'action' => 'edit', 'middleware' => $auth],
    ['method' => 'POST', 'path' => '/user/edit/{id}', 'controller' => UserController::class, 'action' => 'edit', 'middleware' => $auth],
    ['method' => 'POST', 'path' => '/userlist/delete/{id}', 'controller' => UserController::class, 'action' => 'delete', 'middleware' => $auth],

    
    ['method' => 'GET', 'path' => '/products', 'controller' => ProductController::class, 'action' => 'list', 'middleware' => $auth],
    ['method' => 'GET', 'path' => '/product/add', 'controller' => ProductController::class, 'action' => 'add', 'middleware' => $auth],
    ['method' => 'POST', 'path' => '/product/add', 'controller' => ProductController::class, 'action' => 'add', 'middleware' => $auth],
    ['method' => 'GET', 'path' => '/product/edit/{id}', 'controller' => ProductController::class, 'action' => 'edit', 'middleware' => $auth],
    ['method' => 'POST', 'path' => '/product/edit/{id}', 'controller' => ProductController::class, 'action' => 'edit', 'middleware' => $auth],
    ['method' => 'POST', 'path' => '/product/delete/{id}', 'controller' => ProductController::class, 'action' => 'delete', 'middleware' => $auth],

    
    ['method' => 'GET', 'path' => '/category', 'controller' => CategoryController::class, 'action' => 'list', 'middleware' => $auth],
    ['method' => 'GET', 'path' => '/category/add', 'controller' => CategoryController::class, 'action' => 'add', 'middleware' => $auth],
    ['method' => 'POST', 'path' => '/category/add', 'controller' => CategoryController::class, 'action' => 'add', 'middleware' => $auth],
    ['method' => 'GET', 'path' => '/category/edit/{id}', 'controller' => CategoryController::class, 'action' => 'edit', 'middleware' => $auth],
    ['method' => 'POST', 'path' => '/category/edit/{id}', 'controller' => CategoryController::class, 'action' => 'edit', 'middleware' => $auth],
    ['method' => 'POST', 'path' => '/category/delete/{id}', 'controller' => CategoryController::class, 'action' => 'delete', 'middleware' => $auth],

    
    ['method' => 'GET', 'path' => '/supplier', 'controller' => SupplierController::class, 'action' => 'list', 'middleware' => $auth],
    ['method' => 'GET', 'path' => '/supplier/add', 'controller' => SupplierController::class, 'action' => 'add', 'middleware' => $auth],
    ['method' => 'POST', 'path' => '/supplier/add', 'controller' => SupplierController::class, 'action' => 'add', 'middleware' => $auth],
    ['method' => 'GET', 'path' => '/supplier/edit/{id}', 'controller' => SupplierController::class, 'action' => 'edit', 'middleware' => $auth],
    ['method' => 'POST', 'path' => '/supplier/edit/{id}', 'controller' => SupplierController::class, 'action' => 'edit', 'middleware' => $auth],
    ['method' => 'POST', 'path' => '/supplier/delete/{id}', 'controller' => SupplierController::class, 'action' => 'delete', 'middleware' => $auth],

    
    ['method' => 'GET', 'path' => '/order', 'controller' => OrderController::class, 'action' => 'list', 'middleware' => $auth],
    ['method' => 'GET', 'path' => '/order/add', 'controller' => OrderController::class, 'action' => 'add', 'middleware' => $auth],
    ['method' => 'POST', 'path' => '/order/add', 'controller' => OrderController::class, 'action' => 'add', 'middleware' => $auth],
    ['method' => 'GET', 'path' => '/order/edit/{id}', 'controller' => OrderController::class, 'action' => 'edit', 'middleware' => $auth],
    ['method' => 'POST', 'path' => '/order/edit/{id}', 'controller' => OrderController::class, 'action' => 'edit', 'middleware' => $auth],
    ['method' => 'POST', 'path' => '/order/delete/{id}', 'controller' => OrderController::class, 'action' => 'delete', 'middleware' => $auth],
];
