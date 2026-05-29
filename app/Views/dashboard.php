  <?php
  $tasks = $tasks ?? [];
  ?>

  <!DOCTYPE html>
  <html>

      <head>
          <title>Dashboard</title>
      </head>

      <body>

          <h1>Task Manager</h1>

          <p>
              <a href="<?= htmlspecialchars((string)
  $base) ?>/task/create">
                  Create Task
              </a>
          </p>

          <?php include __DIR__ . '/Task/table.php'; ?>

      </body>
  </html>