<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <?php require __DIR__ . '/../head_assets.php'; ?>
  <title>Add category — InventoryCore</title>
</head>
<body class="app">
<main class="page-wrap">
  <header class="page-head">
    <h1 class="page-title">Add category</h1>
    <nav class="page-actions" aria-label="Section actions">
      <a class="btn btn-secondary" href="<?= htmlspecialchars($base) ?>/category">Back to list</a>
    </nav>
  </header>

  <?php if (!empty($errors)): ?>
    <div class="form-error"><?php foreach ($errors as $e): ?><p><?= htmlspecialchars((string)$e) ?></p><?php endforeach; ?></div>
  <?php endif; ?>
  <form method="POST">
    <label for="name">Name</label>
    <input id="name" name="name" value="<?= htmlspecialchars((string)($old['name'] ?? '')) ?>" placeholder="e.g. Electronics" required />
    <button type="submit">Save category</button>
  </form>
</main>
</body>
</html>
