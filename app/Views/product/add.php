<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <?php require __DIR__ . '/../head_assets.php'; ?>
  <title>Add product — InventoryCore</title>
</head>
<body class="app">
<main class="page-wrap">
  <header class="page-head">
    <h1 class="page-title">Add product</h1>
    <nav class="page-actions" aria-label="Section actions">
      <a class="btn btn-secondary" href="<?= htmlspecialchars($base) ?>/products">Back to list</a>
    </nav>
  </header>

  <?php if (!empty($error)): ?><div class="form-error"><?= htmlspecialchars((string)$error) ?></div><?php endif; ?>
  <form method="POST">
    <label>Name</label>
    <input name="name" value="<?= htmlspecialchars((string)($_POST['name'] ?? '')) ?>" required />

    <label>Category</label>
    <select name="category_id" required>
      <option value="">Select category</option>
      <?php foreach ($categories as $c): ?>
        <option value="<?= htmlspecialchars((string)$c['id']) ?>" <?= (string)($_POST['category_id'] ?? '') === (string)$c['id'] ? 'selected' : '' ?>><?= htmlspecialchars((string)$c['name']) ?></option>
      <?php endforeach; ?>
    </select>

    <label>Supplier</label>
    <select name="supplier_id" required>
      <option value="">Select supplier</option>
      <?php foreach ($suppliers as $s): ?>
        <option value="<?= htmlspecialchars((string)$s['id']) ?>" <?= (string)($_POST['supplier_id'] ?? '') === (string)$s['id'] ? 'selected' : '' ?>><?= htmlspecialchars((string)$s['name']) ?></option>
      <?php endforeach; ?>
    </select>

    <label>Quantity</label>
    <input name="quantity" type="number" min="1" value="<?= htmlspecialchars((string)($_POST['quantity'] ?? '1')) ?>" required />

    <label>Price</label>
    <input name="price" type="number" min="1" step="0.01" value="<?= htmlspecialchars((string)($_POST['price'] ?? '100.00')) ?>" required />

    <button type="submit">Save product</button>
  </form>
</main>
</body>
</html>
