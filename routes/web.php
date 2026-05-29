<?php
declare(strict_types=1);

use App\Controllers\TaskController;

return [

    ['method' => 'GET','path' => '/','controller' => TaskController::class,'action' => 'dashboard'],
    ['method' => 'GET','path' => '/dashboard','controller' => TaskController::class,'action' => 'dashboard'],
    
    ['method' => 'GET','path' => '/task','controller' => TaskController::class,'action' => 'list'],
    ['method' => 'GET','path' => '/task/create','controller' => TaskController::class,'action' => 'showCreate'],
    ['method' => 'POST','path' => '/task/create','controller' => TaskController::class,'action' => 'create'],
    ['method' => 'GET','path' => '/task/edit/{id}','controller' => TaskController::class,'action' => 'edit'],
    ['method' => 'POST','path' => '/task/edit/{id}','controller' => TaskController::class,'action' => 'edit'],
    ['method' => 'GET','path' => '/task/delete/{id}','controller' => TaskController::class,'action' => 'showDelete'],
    ['method' => 'POST','path' => '/task/delete/{id}','controller' => TaskController::class,'action' => 'delete'],
    ['method' => 'POST','path' => '/task/complete/{id}','controller' => TaskController::class,'action' => 'complete'],

];
