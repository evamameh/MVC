<?php
$tasks = $tasks ?? [];
?>

<?php if (!empty($tasks)): ?>

    <table border="1" cellpadding="10">

        <tr>
            <th>ID</th>
            <th>Project Name</th>
            <th>Title</th>
            <th>Due Date</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>

        <?php foreach ($tasks as $task): ?>

            <tr>

                <td>
                    <?= htmlspecialchars((string) $task['id']) ?>
                </td>

                <td>
                    <?= htmlspecialchars((string) $task['project_name']) ?>
                </td>

                <td>
                    <?= htmlspecialchars((string) $task['title']) ?>
                </td>

                <td>
                    <?= htmlspecialchars((string) $task['due_date']) ?>
                </td>

                <td>
                    <?= htmlspecialchars((string) $task['status']) ?>
                </td>

                <td>

                    <?php if (strtolower((string) $task['status']) !== 'completed'): ?>

                        <form
                            method="POST"
                            action="<?= htmlspecialchars((string) $base) ?>/task/complete/<?= htmlspecialchars((string) $task['id']) ?>"
                        >
                            <button type="submit">
                                Complete
                            </button>

                        </form>

                    <?php endif; ?>

                    <?php if (strtolower((string) $task['status']) !== 'completed'): ?>

                        <br>

                        <a href="<?= htmlspecialchars((string) $base) ?>/task/edit/<?= htmlspecialchars((string) $task['id']) ?>">
                            Edit
                        </a>

                    <?php endif; ?>

                    <br><br>

                    <a href="<?= htmlspecialchars((string) $base) ?>/task/delete/<?= htmlspecialchars((string) $task['id']) ?>">
                        Delete
                    </a>

                </td>

            </tr>

        <?php endforeach; ?>

    </table>

<?php else: ?>

    <p>No tasks found.</p>

<?php endif; ?>
