# MVC — InventoryCore

Custom PHP 8.3 MVC framework (**Core**) plus **InventoryCore** MVP (**App**): inventory with products, categories, suppliers, orders, and users.

## Project structure

```
MVC/
├── app/                    # Application layer (MVP)
│   ├── Controllers/
│   ├── Contracts/        # Repository interfaces (DIP)
│   ├── Models/
│   ├── Views/
│   └── Middleware/
├── core/                   # Framework layer
│   ├── Application.php
│   ├── Container/Container.php
│   ├── Database/ORM.php, Model.php, QueryBuilder.php
│   ├── Http/Request.php, Response.php, Router.php, RouteMatcher.php, Dispatcher.php
│   └── View/Engine.php
├── config/app.php, database.php
├── public/index.php        # Front controller
├── routes/web.php
├── inventory.sql
├── composer.json
├── README.md
└── SOLID-JUSTIFICATION.md
```

## Requirements

- PHP **8.3+**
- Composer
- MySQL / MariaDB

## Setup

1. Open the project folder **`MVC`** (e.g. `C:\jspec2a03\phpsite\MVC`).

2. Install dependencies:

   ```bash
   cd MVC
   composer install
   ```

3. Import the database:

   ```bash
   mysql -u root -p < inventory.sql
   ```

4. Edit `config/database.php` (host, database, username, password).

5. Edit `config/app.php`:

   - `base_path` → `/MVC/public` (match your local URL)

6. Run in browser:

   `http://localhost:8080/MVC/public/index.php/login`

7. Register at `/register`, then login.

## Request flow

```
public/index.php
  → vendor/autoload.php (PSR-4: Core\, App\)
  → Core\Application::bootstrap()
  → routes/web.php
  → Core\Http\Dispatcher
  → App\Middleware\AuthMiddleware (when required)
  → App\Controllers\*
  → App\Models\* (ORM → MySQL)
  → Core\View\Engine → app/Views/*.php
```

## Framework design

| Class | File | Role |
|-------|------|------|
| Application | `core/Application.php` | Bootstrap, DI container, run app |
| Container | `core/Container/Container.php` | `bind`, `singleton`, `get` |
| Router | `core/Http/Router.php` | Match method + path (`{id}` params) |
| RouteMatcher | `core/Http/RouteMatcher.php` | Router implementation (regex matching) |
| Dispatcher | `core/Http/Dispatcher.php` | Middleware + call controller |
| Request | `core/Http/Request.php` | HTTP input |
| Response | `core/Http/Response.php` | `redirect()`, `json()`, `html()` |
| Engine | `core/View/Engine.php` | Render views, `$base` for links |
| ORM | `core/Database/ORM.php` | **Object-Relational Mapping** — PDO, `table()`, `model()`, `transaction()` |
| Model | `core/Database/Model.php` | Base ORM model — each subclass sets `$table` (object ↔ table) |
| QueryBuilder | `core/Database/QueryBuilder.php` | `from()`, `where()`, `get()`, `insert()`, `update()`, `delete()` |

Controllers do not contain SQL. Models in `app/Models/` extend `Core\Database\Model`, declare `protected static string $table`, and use `$this->query()` (scoped to that table) via the ORM.

**Dependency inversion:** `LoginController` and `ProductController` depend on `UserRepositoryInterface` and `ProductRepositoryInterface` (`app/Contracts/`). `Application::bootstrap()` binds those interfaces to `User` and `Product`.

## MVP (InventoryCore)

- Login, register, logout
- Dashboard
- Products, categories, suppliers, orders (CRUD)
- User admin (`/userlist`)

## Routes (from `routes/web.php`)

Base: `/MVC/public/index.php`

**Public:** `GET/POST /login`, `GET/POST /register`, `GET /logout`

**HTML (login required):**

- `/dashboard`, `/products`, `/product/add`, `/product/edit/{id}`, `POST /product/delete/{id}`
- `/category`, `/category/add`, `/category/edit/{id}`, `POST /category/delete/{id}`
- `/supplier`, `/supplier/add`, `/supplier/edit/{id}`, `POST /supplier/delete/{id}`
- `/order`, `/order/add`, `/order/edit/{id}`, `POST /order/delete/{id}`
- `/userlist`, `/userlist/create`, `/user/edit/{id}`, `POST /userlist/delete/{id}`

## PSR-4 (`composer.json`)

- `Core\` → `core/`
- `App\` → `app/`

Only `public/index.php` uses `require` for autoload.

## Deliverables

- [x] Custom MVC framework
- [x] MVP CRUD application
- [x] `composer.json`
- [x] `README.md`
- [x] `SOLID-JUSTIFICATION.md`
- [ ] GitHub private repo + evaluator (submit yourself)
