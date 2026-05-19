<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <?php require __DIR__ . '/../head_assets.php'; ?>
  <title>Add supplier — InventoryCore</title>
</head>
<body class="app">
<main class="page-wrap">
  <header class="page-head">
    <h1 class="page-title">Add supplier</h1>
    <nav class="page-actions" aria-label="Section actions">
      <a class="btn btn-secondary" href="<?= htmlspecialchars($base) ?>/supplier">Back to list</a>
    </nav>
  </header>

  <?php if (!empty($error)): ?><div class="form-error"><?= htmlspecialchars((string)$error) ?></div><?php endif; ?>
  <form method="POST">
    <label>Name</label>
    <input name="name" required value="<?= htmlspecialchars((string)($_POST['name'] ?? '')) ?>" />
    <label>Contact</label>
    <input name="contact" required value="<?= htmlspecialchars((string)($_POST['contact'] ?? '')) ?>" />
    <button type="submit">Save supplier</button>
  </form>
</main>
</body>
</html>
