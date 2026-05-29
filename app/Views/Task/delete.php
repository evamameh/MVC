<?php
$base = (string) ($base ?? '');
$task = $task ?? [];
?>

<!DOCTYPE html>
<html>

<head>
    <title>Delete Task</title>
</head>

<body>

<dialog open>
    <h1>Delete Task</h1>

    <p>
        Are you sure you want to delete
        "<?= htmlspecialchars((string) ($task['title'] ?? 'this task')) ?>"?
    </p>

    <form
        method="POST"
        action="<?= htmlspecialchars($base) ?>/task/delete/<?= htmlspecialchars((string) ($task['id'] ?? '')) ?>"
    >
        <button type="submit">
            Delete
        </button>

        <a href="<?= htmlspecialchars($base) ?>/dashboard">
            Cancel
        </a>
    </form>
</dialog>

</body>
</html>
