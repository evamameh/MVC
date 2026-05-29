# Design Notes

This project is split into a framework layer and a task layer. The goal is to keep routing, rendering, and database work separate from the task rules themselves.

## Why The Structure Is Split

The code is arranged so each file has one clear job:

- `Core\Application` builds the app and wires the objects together.
- `Core\Http\Request` reads the request data in one place.
- `Core\Http\Router` decides which route matches the URL.
- `Core\Http\Dispatcher` runs the matched controller method.
- `Core\View\Engine` renders a PHP file into HTML.
- `Core\Database\Connection` wraps PDO.
- `Core\Database\Model` acts as the shared base for app models.
- `Core\Database\QueryBuilder` builds the SQL used by the models.
- `App\Controllers\TaskController` handles create, edit, delete, and complete actions.
- `App\Models\Task` handles the `tasks` table.
- `App\Contracts\TaskRepositoryContract` keeps the controller dependent on task behavior instead of a hard-coded class.

## How The Principles Appear In The Code

### Single Responsibility
Each core class does one thing. The controller handles task flow, the model handles persistence, the view engine renders templates, and the router only matches routes.

### Open/Closed
New routes can be added in `routes/web.php` without rewriting the router or dispatcher. New models can extend `Core\Database\Model` and use the same query layer.

### Liskov Substitution
`TaskController` depends on the task contract, so any class that follows the same task methods can be swapped in without changing the controller logic.

### Interface Segregation
The task contract only includes the methods the controller actually needs: list, find, create, update, delete, and complete.

### Dependency Inversion
High-level code does not build low-level classes inside the controller. `Core\Application` creates the objects and passes them into the controller.

## Database Layer

The model and query builder work together:
- `Task` extends `Model`
- `Model` calls `Connection`
- `Connection` returns `QueryBuilder`
- `QueryBuilder` runs the SQL

That is why the controller does not contain raw SQL.

## Defense Point

If asked why the project is organized this way, the short answer is:

- the framework code is in `core/`
- the task logic is in `app/`
- the route file decides the URL flow
- the controller handles validation and redirects
- the model and query builder handle database work
- the view engine handles page output
