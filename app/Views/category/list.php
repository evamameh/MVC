<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <?php require __DIR__ . '/../head_assets.php'; ?>
  <title>Categories — InventoryCore</title>
</head>
<body class="app">
<main class="page-wrap">
  <header class="page-head">
    <h1 class="page-title">Categories</h1>
    <nav class="page-actions" aria-label="Section actions">
      <a class="btn btn-primary" href="<?= htmlspecialchars($base) ?>/category/add">+ Add category</a>
      <a class="btn btn-secondary" href="<?= htmlspecialchars($base) ?>/dashboard">Dashboard</a>
    </nav>
  </header>

  <?php if (!empty($success)): ?><p class="flash flash--success"><?= htmlspecialchars((string)$success) ?></p><?php endif; ?>
  <?php if (!empty($error)): ?><p class="flash flash--error"><?= htmlspecialchars((string)$error) ?></p><?php endif; ?>

  <div class="table-wrap">
    <table>
      <thead><tr><th>Name</th><th>Actions</th></tr></thead>
      <tbody>
      <?php foreach ($categories as $c): ?>
        <tr>
          <td><?= htmlspecialchars((string)($c['name'] ?? '')) ?></td>
          <td>
            <nav aria-label="Row actions">
              <a class="btn btn-sm btn-secondary" href="<?= htmlspecialchars($base) ?>/category/edit/<?= htmlspecialchars((string)$c['id']) ?>">Edit</a>
              <form method="post" action="<?= htmlspecialchars($base) ?>/category/delete/<?= htmlspecialchars((string)$c['id']) ?>" onsubmit="return confirm('Delete this category?');">
                <button class="btn btn-sm btn-danger" type="submit">Delete</button>
              </form>
            </nav>
          </td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</main>
</body>
</html>
