<?php
declare(strict_types=1);






namespace Core;

use App\Controllers\CategoryController;
use App\Controllers\DashboardController;
use App\Controllers\LoginController;
use App\Controllers\OrderController;
use App\Controllers\ProductController;
use App\Controllers\RegisterController;
use App\Controllers\SupplierController;
use App\Controllers\UserController;
use App\Middleware\AuthMiddleware;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\Supplier;
use App\Contracts\ProductRepositoryInterface;
use App\Contracts\UserRepositoryInterface;
use App\Models\User;
use Core\Container\Container;
use Core\Database\Connection;
use Core\Http\Dispatcher;
use Core\Http\Request;
use Core\Http\Router;
use Core\View\Engine;

final class Application
{
    
    private Container $container;

    
    private string $basePath;

    private function __construct(Container $container, string $basePath)
    {
        $this->container = $container;
        $this->basePath = $basePath;
    }

    
    public static function bootstrap(string $basePath): self
    {
        $container = new Container();

        
        $appConfig = require $basePath . '/config/app.php';       
        $dbConfig = require $basePath . '/config/database.php';   
        $urlBase = (string) ($appConfig['base_path'] ?? '');

        AuthMiddleware::setConfig($appConfig); 

        
        $connection = Connection::fromConfig($dbConfig); 
        $router = new Router();                            
        $request = Request::fromGlobals();                  
        $viewsPath = $basePath . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'Views';
        $view = new Engine($viewsPath, $urlBase);          

        
        $category = new Category($connection);
        $product = new Product($connection);
        $order = new Order($connection);
        $supplier = new Supplier($connection);
        $user = new User($connection);

        
        $container->instance(Connection::class, $connection);
        $container->instance(Router::class, $router);
        $container->instance(Request::class, $request);
        $container->instance(Engine::class, $view);

        $container->instance(Category::class, $category);
        $container->instance(Product::class, $product);
        $container->instance(Order::class, $order);
        $container->instance(Supplier::class, $supplier);
        $container->instance(User::class, $user);

        
        $container->instance(UserRepositoryInterface::class, $user);
        $container->instance(ProductRepositoryInterface::class, $product);

        
        $container->instance(DashboardController::class, new DashboardController($view));
        $container->instance(LoginController::class, new LoginController($view, $user));
        $container->instance(RegisterController::class, new RegisterController($view, $user));
        $container->instance(UserController::class, new UserController($view, $user));
        $container->instance(CategoryController::class, new CategoryController($view, $category));
        $container->instance(ProductController::class, new ProductController($view, $product, $category, $supplier));
        $container->instance(SupplierController::class, new SupplierController($view, $supplier));
        $container->instance(OrderController::class, new OrderController($view, $order, $product, $category, $connection));

        
        $container->instance(Dispatcher::class, new Dispatcher($router, $container));

        return new self($container, $basePath);
    }

    
    public function run(): void
    {
        
        $routes = require $this->basePath . '/routes/web.php';

        $this->container->get(Dispatcher::class)->handle(
            $this->container->get(Request::class),
            $routes,
        );
    }
}
