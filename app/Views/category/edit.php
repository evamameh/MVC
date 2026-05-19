<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <?php require __DIR__ . '/../head_assets.php'; ?>
  <title>Edit category — InventoryCore</title>
</head>
<body class="app">
<main class="page-wrap">
  <header class="page-head">
    <h1 class="page-title">Edit category</h1>
    <nav class="page-actions" aria-label="Section actions">
      <a class="btn btn-secondary" href="<?= htmlspecialchars($base) ?>/category">Back to list</a>
    </nav>
  </header>

  <?php if (!empty($errors)): ?>
    <div class="form-error"><?php foreach ($errors as $e): ?><p><?= htmlspecialchars((string)$e) ?></p><?php endforeach; ?></div>
  <?php endif; ?>
  <form id="editForm" method="POST">
    <label for="name">Name</label>
    <input id="name" name="name" value="<?= htmlspecialchars((string)($category['name'] ?? '')) ?>" required />
    <button id="updateBtn" type="submit">Save changes</button>
  </form>
</main>
<script>
  (() => {
    const form = document.getElementById('editForm');
    const updateBtn = document.getElementById('updateBtn');
    if (!form || !updateBtn) return;
    const initial = JSON.stringify(Object.fromEntries(new FormData(form).entries()));
    const sync = () => {
      const current = JSON.stringify(Object.fromEntries(new FormData(form).entries()));
      updateBtn.disabled = current === initial;
    };
    form.addEventListener('input', sync);
    form.addEventListener('change', sync);
    sync();
  })();
</script>
</body>
</html>
