<?php
declare(strict_types=1);

use Core\Application;

session_start();

require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

Application::bootstrap(dirname(__DIR__))->run();
