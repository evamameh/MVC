<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Contracts\TaskRepositoryContract;
use Core\View\Engine;

class TaskController
{
    private TaskRepositoryContract $tasks;

    private Engine $view;

    private string $basePath;

    public function __construct(
        Engine $view,
        TaskRepositoryContract $tasks,
        string $basePath
    ) {
        $this->view = $view;
        $this->tasks = $tasks;
        $this->basePath = rtrim($basePath, '/');
    }

    public function dashboard(): string
    {
        $tasks = $this->tasks->allTasks();

        return $this->view->render('dashboard', [
            'tasks' => $tasks
        ]);
    }

    public function list(): string
    {
        $tasks = $this->tasks->allTasks();

        return $this->view->render('dashboard', [
            'tasks' => $tasks
        ]);
    }

    public function showCreate(): string
    {
        return $this->view->render('Task/create');
    }

    public function create(): string
    {
        $input = $this->taskInputFromPost();
        $errors = $this->validateTaskInput($input);

        if ($errors !== []) {
            return $this->view->render('Task/create', [
                'errors' => $errors
            ]);
        }

        $this->tasks->createTask([
            'project_name' => $input['project_name'],
            'title' => $input['title'],
            'due_date' => $input['due_date'],
            'status' => 'Pending'
        ]);

        return $this->redirect('/dashboard');
    }

    public function edit(int $id): string
    {
        $task = $this->tasks->findTask($id);

        if (!$task || strtolower((string) $task['status']) === 'completed') {
            return $this->redirect('/dashboard');
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->view->render('Task/edit', [
                'task' => $task
            ]);
        }

        $input = $this->taskInputFromPost();
        $errors = $this->validateTaskInput($input);

        if ($errors !== []) {
            return $this->view->render('Task/edit', [
                'task' => array_merge($task, $input),
                'errors' => $errors
            ]);
        }

        $this->tasks->updateTask($id, [
            'project_name' => $input['project_name'],
            'title' => $input['title'],
            'due_date' => $input['due_date']
        ]);

        return $this->redirect('/dashboard');
    }

    public function complete(int $id): string
    {
        $this->tasks->completeTask($id);

        return $this->redirect('/dashboard');
    }

    public function showDelete(int $id): string
    {
        $task = $this->tasks->findTask($id);

        if (!$task) {
            return $this->redirect('/dashboard');
        }

        return $this->view->render('Task/delete', [
            'task' => $task
        ]);
    }

    public function delete(int $id): string
    {
        if (!$this->tasks->findTask($id)) {
            return $this->redirect('/dashboard');
        }

        $this->tasks->deleteTask($id);

        return $this->redirect('/dashboard');
    }

    private function taskInputFromPost(): array
    {
        return [
            'project_name' => trim($_POST['project_name'] ?? ''),
            'title' => trim($_POST['title'] ?? ''),
            'due_date' => trim($_POST['due_date'] ?? ''),
        ];
    }

    private function validateTaskInput(array $input): array
    {
        $errors = [];

        if ($input['project_name'] === '') {
            $errors[] = 'Project name is required.';
        }

        if ($input['title'] === '') {
            $errors[] = 'Title is required.';
        }

        if ($input['due_date'] === '') {
            $errors[] = 'Due date is required.';
        }

        if ($input['due_date'] !== '') {
            $date = \DateTimeImmutable::createFromFormat('Y-m-d', $input['due_date']);

            if (!$date || $date->format('Y-m-d') !== $input['due_date']) {
                $errors[] = 'Due date must be a valid date.';
            }
        }

        if (
            $input['project_name'] !== ''
            && !preg_match('/^[a-zA-Z ]+$/', $input['project_name'])
        ) {
            $errors[] = 'Project name must contain letters and spaces only.';
        }

        if (
            $input['title'] !== ''
            && !preg_match('/^[a-zA-Z ]+$/', $input['title'])
        ) {
            $errors[] = 'Title must contain letters and spaces only.';
        }

        return $errors;
    }

    private function redirect(string $path): string
    {
        header('Location: ' . $this->resolveUrl($path));

        return '';
    }

    private function resolveUrl(string $path): string
    {
        if (preg_match('#^https?://#i', $path) === 1) {
            return $path;
        }

        return rtrim($this->basePath, '/') . '/' . ltrim($path, '/');
    }
}
