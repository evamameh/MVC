<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <?php require __DIR__ . '/../head_assets.php'; ?>
  <title>Edit product — InventoryCore</title>
</head>
<body class="app">
<main class="page-wrap">
  <header class="page-head">
    <h1 class="page-title">Edit product</h1>
    <nav class="page-actions" aria-label="Section actions">
      <a class="btn btn-secondary" href="<?= htmlspecialchars($base) ?>/products">Back to list</a>
    </nav>
  </header>

  <?php if (!empty($error)): ?><div class="form-error"><?= htmlspecialchars((string)$error) ?></div><?php endif; ?>
  <form id="editForm" method="POST">
    <label>Name</label>
    <input name="name" value="<?= htmlspecialchars((string)$product['name']) ?>" required />

    <label>Category</label>
    <select name="category_id" required>
      <?php foreach ($categories as $c): ?>
        <option value="<?= htmlspecialchars((string)$c['id']) ?>" <?= ((string)$c['id']===(string)$product['category_id'])?'selected':'' ?>><?= htmlspecialchars((string)$c['name']) ?></option>
      <?php endforeach; ?>
    </select>

    <label>Supplier</label>
    <select name="supplier_id" required>
      <?php foreach ($suppliers as $s): ?>
        <option value="<?= htmlspecialchars((string)$s['id']) ?>" <?= ((string)$s['id']===(string)$product['supplier_id'])?'selected':'' ?>><?= htmlspecialchars((string)$s['name']) ?></option>
      <?php endforeach; ?>
    </select>

    <label>Quantity</label>
    <input name="quantity" type="number" min="1" value="<?= htmlspecialchars((string)$product['quantity']) ?>" required />

    <label>Price</label>
    <input name="price" type="number" min="1" step="0.01" value="<?= htmlspecialchars((string)$product['price']) ?>" required />

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
