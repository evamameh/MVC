# My MVC Framework

This project is a custom PHP MVC application for managing tasks. It uses object-oriented PHP, a custom router and dispatcher, a lightweight database layer, and PSR-4 autoloading through Composer.

## What It Does

- creates tasks
- shows tasks on the dashboard
- edits task details
- deletes tasks
- marks a task as completed

## Main Parts

- `app/` holds the task code
- `core/` holds the framework code
- `routes/` defines which URL goes to which controller method
- `config/` stores app and database settings
- `public/` is the entry point
- `task_manager.sql` contains the database schema

## How The Request Flow Works

1. `public/index.php` loads Composer autoload.
2. `Core\Application` creates the core objects.
3. `routes/web.php` returns the available routes.
4. `Core\Http\Router` finds the matching route.
5. `Core\Http\Dispatcher` calls the controller method.
6. `App\Controllers\TaskController` handles the task action.
7. `App\Models\Task` talks to the database through the model layer.
8. `Core\View\Engine` renders the PHP view file.

## Framework Layer

- `Core\Http\Request` normalizes the request path and method
- `Core\Http\Router` matches the URL pattern
- `Core\Http\Dispatcher` invokes the controller action
- `Core\Container\Container` stores registered objects
- `Core\Database\Connection` creates the PDO connection
- `Core\Database\Model` provides the shared model behavior
- `Core\Database\QueryBuilder` builds the SQL statements
- `Core\View\Engine` loads the views

## App Layer

- `TaskController` validates input and controls the task flow
- `Task` saves and fetches records from the `tasks` table
- `TaskRepositoryContract` defines the task operations the controller expects

## Database

The app uses a `tasks` table with:
- `id`
- `project_name`
- `title`
- `due_date`
- `status`

## Routes

- `GET /` -> dashboard
- `GET /dashboard` -> dashboard
- `GET /task` -> task list
- `GET /task/create` -> create form
- `POST /task/create` -> save task
- `GET /task/edit/{id}` -> edit form
- `POST /task/edit/{id}` -> update task
- `GET /task/delete/{id}` -> delete confirmation
- `POST /task/delete/{id}` -> delete task
- `POST /task/complete/{id}` -> mark completed

## Run

1. Install dependencies:

```bash
composer install
```

2. Start the built-in PHP server:

```bash
php -S localhost:8080 -t public
```

3. Open the app in the browser:

```text
http://localhost:8080/
```

4. If the page does not load, make sure you are running the command from the project root folder.
