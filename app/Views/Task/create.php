<?php
$errors = $errors ?? [];
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Create Task</title>

</head>

<body>

    <h1>Create Task</h1>

    <?php if (!empty($errors)): ?>

        <div>

            <?php foreach ($errors as $error): ?>

                <p>
                    <?= htmlspecialchars($error) ?>
                </p>

            <?php endforeach; ?>

        </div>

        <br>

    <?php endif; ?>

    <form
        method="POST"
        action="<?= htmlspecialchars($base) ?>/task/create"
    >

        <div>

            <label for="project_name">
                Project Name
            </label>

            <br><br>

            <input
                type="text"
                id="project_name"
                name="project_name"
                placeholder="Enter project name"
            >

        </div>

        <br><br>

        <div>

            <label for="title">
                Title
            </label>

            <br><br>

            <input
                type="text"
                id="title"
                name="title"
                placeholder="Enter task title"
            >

        </div>

        <br><br>

        <div>

            <label for="due_date">
                Due Date
            </label>

            <br><br>

            <input
                type="date"
                id="due_date"
                name="due_date"
            >

        </div>

        <br><br>

        <button type="submit">
            Save Task
        </button>

        <a href="<?= htmlspecialchars($base) ?>/dashboard">
            Back
        </a>

    </form>

</body>
</html>
