<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <?php require __DIR__ . '/../head_assets.php'; ?>
  <title>Suppliers — InventoryCore</title>
</head>
<body class="app">
<main class="page-wrap">
  <header class="page-head">
    <h1 class="page-title">Suppliers</h1>
    <nav class="page-actions" aria-label="Section actions">
      <a class="btn btn-primary" href="<?= htmlspecialchars($base) ?>/supplier/add">+ Add supplier</a>
      <a class="btn btn-secondary" href="<?= htmlspecialchars($base) ?>/dashboard">Dashboard</a>
    </nav>
  </header>

  <?php if (!empty($success)): ?><p class="flash flash--success"><?= htmlspecialchars((string)$success) ?></p><?php endif; ?>
  <?php if (!empty($error)): ?><p class="flash flash--error"><?= htmlspecialchars((string)$error) ?></p><?php endif; ?>

  <?php if (!empty($suppliers)): ?>
  <div class="table-wrap">
    <table>
      <thead><tr><th>Name</th><th>Contact</th><th>Actions</th></tr></thead>
      <tbody>
      <?php foreach ($suppliers as $supplier): ?>
        <tr>
          <td><?= htmlspecialchars((string)$supplier['name']) ?></td>
          <td><?= htmlspecialchars((string)$supplier['contact']) ?></td>
          <td>
            <nav aria-label="Row actions">
              <a class="btn btn-sm btn-secondary" href="<?= htmlspecialchars($base) ?>/supplier/edit/<?= htmlspecialchars((string)$supplier['id']) ?>">Edit</a>
              <form method="post" action="<?= htmlspecialchars($base) ?>/supplier/delete/<?= htmlspecialchars((string)$supplier['id']) ?>" onsubmit="return confirm('Delete this supplier?');">
                <button class="btn btn-sm btn-danger" type="submit">Delete</button>
              </form>
            </nav>
          </td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <?php else: ?>
    <p class="empty-state">No suppliers yet. Add a supplier to link products to vendors.</p>
  <?php endif; ?>
</main>
</body>
</html>
