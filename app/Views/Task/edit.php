<?php
$errors = $errors ?? [];
?>

<!DOCTYPE html>
<html>

<head>
    <title>Edit Task</title>
</head>

<body>

    <h1>Edit Task</h1>

    <?php if (!empty($errors)): ?>
        <?php foreach ($errors as $error): ?>
            <p><?= htmlspecialchars($error) ?></p>

        <?php endforeach; ?>
    <?php endif; ?>

    <form method="POST">

        <p>Task ID</p>

        <input type="text"
            id="task_id"
            value="<?= htmlspecialchars((string) $task['id']) ?>"
            readonly
        >

        <br><br>

        <p>Project Name</p>

        <input
            type="text"
            id="project_name"
            name="project_name"
            value="<?= htmlspecialchars((string) $task['project_name']) ?>"
        >

        <br><br>

        <p>Title</p>

        <input
            type="text"
            id="title"
            name="title"
            value="<?= htmlspecialchars((string) $task['title']) ?>"
        >

        <br><br>

        <p>Due Date</p>

        <input
            type="date"
            id="due_date"
            name="due_date"
            value="<?= htmlspecialchars((string) $task['due_date']) ?>"
        >

        <br><br>

        <button
            type="submit"
            id="updateBtn"
        >
            Update Task
        </button>

    </form>

    <br>

    <a href="<?= htmlspecialchars($base) ?>/dashboard">
        Back
    </a>
</body>
</html>
