<?php
declare(strict_types=1);

namespace Core;

use App\Controllers\TaskController;

use App\Models\Task;
use App\Contracts\TaskRepositoryContract;

use Core\Container\Container;
use Core\Database\Connection;
use Core\Http\Dispatcher;
use Core\Http\Request;
use Core\Http\Router;
use Core\View\Engine;

final class Application {

    private Container $container;

    private string $basePath;

    private function __construct(Container $container, string $basePath) {
        $this->container = $container;
        $this->basePath = $basePath;
    }

    public static function bootstrap(string $basePath): self {

        $container = new Container();

        $appConfig = require $basePath . '/config/app.php';
        $dbConfig = require $basePath . '/config/database.php';

        $urlBase = (string) ($appConfig['base_path'] ?? '');

        $connection = Connection::fromConfig($dbConfig);

        $router = new Router();

        $request = Request::fromGlobals();

        $viewsPath =
            $basePath .
            DIRECTORY_SEPARATOR .
            'app' .
            DIRECTORY_SEPARATOR .
            'Views';

        $view = new Engine($viewsPath, $urlBase);

        $task = new Task($connection);

        $container->instance(Connection::class, $connection);
        $container->instance(Router::class, $router);
        $container->instance(Request::class, $request);
        $container->instance(Engine::class, $view);

        $container->instance(Task::class, $task);
        $container->instance(TaskRepositoryContract::class, $task);

        $container->instance(
            TaskController::class,
            new TaskController($view, $task, $urlBase)
        );

        $container->instance(
            Dispatcher::class,
            new Dispatcher($router, $container)
        );

        return new self($container, $basePath);
    }

    public function run(): void {

        $routes = require $this->basePath . '/routes/web.php';

        $this->container
            ->get(Dispatcher::class)
            ->handle(
                $this->container->get(Request::class),
                $routes
            );
    }
}
