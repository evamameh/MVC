<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <?php require __DIR__ . '/../head_assets.php'; ?>
  <title>Edit supplier — InventoryCore</title>
</head>
<body class="app">
<main class="page-wrap">
  <header class="page-head">
    <h1 class="page-title">Edit supplier</h1>
    <nav class="page-actions" aria-label="Section actions">
      <a class="btn btn-secondary" href="<?= htmlspecialchars($base) ?>/supplier">Back to list</a>
    </nav>
  </header>

  <?php if (!empty($error)): ?><div class="form-error"><?= htmlspecialchars((string)$error) ?></div><?php endif; ?>
  <form id="editForm" method="POST">
    <label>Name</label>
    <input name="name" required value="<?= htmlspecialchars((string)($_POST['name'] ?? ($supplier['name'] ?? ''))) ?>" />
    <label>Contact</label>
    <input name="contact" required value="<?= htmlspecialchars((string)($_POST['contact'] ?? ($supplier['contact'] ?? ''))) ?>" />
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
