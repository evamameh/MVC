# SOLID Design Justification — MVC / InventoryCore

This document explains how the five SOLID principles appear in **this repository** (`MVC` project: `core/` framework + `app/` application).

---

## ORM — Object-Relational Mapping

**ORM** maps **objects** (PHP model classes) to **relational** data (MySQL tables).

| Layer | Class | Role |
|-------|-------|------|
| ORM | `Core\Database\ORM` | PDO from `config/database.php`; `table()`, `model()`, `transaction()` |
| Model | `Core\Database\Model` | Base class; each app model sets `protected static string $table` |
| Query | `Core\Database\QueryBuilder` | Fluent SQL (SELECT/INSERT/UPDATE/DELETE) |

**Table mapping (object ↔ relation):**

| Model (`app/Models/`) | Table (`inventory.sql`) |
|------------------------|-------------------------|
| `User` | `users` |
| `Category` | `categories` |
| `Supplier` | `suppliers` |
| `Product` | `products` |
| `Order` | `orders` |

**Example:** `Product` uses `protected static string $table = 'products'`. Listing products runs through the ORM, not raw SQL in controllers:

```php
// App\Models\Product
return $this->query()->orderBy('id', 'DESC')->get();
```

`Application::bootstrap()` creates `ORM::fromConfig($dbConfig)` and injects it into every model: `new Product($orm)`.

---

## S — Single Responsibility

Each class has one main job.

| Class | Responsibility |
|-------|----------------|
| `Core\Http\Router` | Match HTTP method and URI to a route |
| `Core\Http\RouteMatcher` | Router matching logic (used by `Router`) |
| `Core\Http\Dispatcher` | Run middleware, then the route handler |
| `Core\Http\Request` | Wrap request method, path, and body |
| `Core\Http\Response` | Send redirect / JSON / HTML |
| `Core\View\Engine` | Render `app/Views` templates |
| `Core\Database\ORM` | Object-Relational Mapping (PDO + queries + transactions) |
| `Core\Database\Model` | Base ORM model — table mapping + `query()` |
| `Core\Database\QueryBuilder` | Build SELECT/INSERT/UPDATE/DELETE |
| `Core\Container\Container` | Dependency injection |
| `App\Models\Product` | Product rules; uses QueryBuilder + PDO where needed |
| `App\Controllers\ProductController` | HTTP for products only (no SQL in views) |

**Example:** `ProductController::list()` calls `$this->products->findAll()` and `$this->render('product/list', ...)`. Data access is in `App\Models\Product` via the ORM (`$this->query()->...` on table `products`); HTML is in `app/Views/product/list.php`.

---

## O — Open / Closed

You can extend behaviour without changing core routing code.

- New routes → add rows in `routes/web.php` only.
- New controller → register in `Core\Application::bootstrap()` with `$container->bind(...)`.
- New entity → new `App\Controllers\*`, `App\Models\*`, and views under `app/Views/`.

`Dispatcher` and `RouteMatcher` stay the same when you add features.

---

## L — Liskov Substitution

Subtypes must work wherever the parent type is expected.

- `Router` extends `RouteMatcher` and is used by `Dispatcher` wherever a matcher is needed.
- `User` implements `UserRepositoryInterface`; controllers type-hint the interface and receive `User` from the container.
- `Product` implements `ProductRepositoryInterface` the same way.

---

## I — Interface Segregation

Classes should not depend on methods they never use.

- `App\Contracts\UserRepositoryInterface` — user methods only.
- `App\Contracts\ProductRepositoryInterface` — product CRUD only.
- `CategoryController` only receives `Category`, not `Product` or `Order`.
- `AuthMiddleware` only exposes auth checks (`requireSession`, `ensureStarted`).

---

## D — Dependency Inversion

High-level code does not create low-level objects with `new` inside controllers.

1. **`Core\Container\Container`** stores how to build each class.

2. **`Core\Application::bootstrap()`** wires dependencies, for example:

```php
$container->singleton(ProductRepositoryInterface::class,
    static fn (Container $c): ProductRepositoryInterface => $c->get(Product::class));

$container->bind(ProductController::class, static fn (Container $c): ProductController =>
    new ProductController(
        $c->get(Engine::class),
        $c->get(ProductRepositoryInterface::class),
        $c->get(Category::class),
        $c->get(Supplier::class),
    ));
```

3. **`ProductController`** constructor type-hints `ProductRepositoryInterface`, not `Product` directly.

Routes use `$container->get(ProductController::class)` from `routes/web.php`, so controllers are resolved through the container.

---

## Summary table

| Principle | Evidence in MVC project |
|-----------|-------------------------|
| **S** | Router/Dispatcher/Engine/ORM separate from models and controllers |
| **O** | New routes and controllers via config + container |
| **L** | `Router` / `RouteMatcher`; models implement repository interfaces |
| **I** | Separate user vs product contracts; focused middleware |
| **D** | Container + interface bindings + constructor injection |

---

## Conclusion

- **Framework (`core/`):** HTTP, routing, views, DI.
- **Application (`app/`):** InventoryCore business logic.

Together they satisfy MVC separation and SOLID-oriented design for the final examination.

*References: `core/Database/ORM.php`, `core/Database/Model.php`, `core/Application.php`, `core/Container/Container.php`, `core/Http/Router.php`, `app/Contracts/ProductRepositoryInterface.php`, `app/Controllers/ProductController.php`, `app/Models/Product.php`, `routes/web.php`.*
