<?php
$categoryNameById = $categoryNameById ?? [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <?php require __DIR__ . '/../head_assets.php'; ?>
  <title>Products — InventoryCore</title>
</head>
<body class="app">
<main class="page-wrap">
  <header class="page-head">
    <h1 class="page-title">Products</h1>
    <nav class="page-actions" aria-label="Section actions">
      <a class="btn btn-primary" href="<?= htmlspecialchars($base) ?>/product/add">+ Add product</a>
      <a class="btn btn-secondary" href="<?= htmlspecialchars($base) ?>/dashboard">Dashboard</a>
    </nav>
  </header>

  <?php if (!empty($success)): ?><p class="flash flash--success"><?= htmlspecialchars((string)$success) ?></p><?php endif; ?>
  <?php if (!empty($error)): ?><p class="flash flash--error"><?= htmlspecialchars((string)$error) ?></p><?php endif; ?>

  <?php if (!empty($products)): ?>
  <div class="table-wrap">
    <table>
      <thead><tr><th>Name</th><th>Category</th><th>Qty</th><th>Unit price</th><th>Total price</th><th>Actions</th></tr></thead>
      <tbody>
      <?php foreach ($products as $product): ?>
        <?php
          $unit = (float) ($product['price'] ?? 0);
          $qty = (int) ($product['quantity'] ?? 0);
          $totalPrice = $unit * $qty;
          $cid = (string) ($product['category_id'] ?? '');
          $cname = $categoryNameById[$cid] ?? '—';
          $pid = (string) ($product['id'] ?? '');
        ?>
        <tr>
          <td><?= htmlspecialchars((string)$product['name']) ?></td>
          <td><?= htmlspecialchars($cname) ?></td>
          <td><?= htmlspecialchars((string)$product['quantity']) ?></td>
          <td><?= htmlspecialchars(number_format($unit, 2, '.', ',')) ?></td>
          <td><?= htmlspecialchars(number_format($totalPrice, 2, '.', ',')) ?></td>
          <td>
            <nav aria-label="Row actions">
              <a class="btn btn-sm btn-secondary" href="<?= htmlspecialchars($base) ?>/product/edit/<?= htmlspecialchars($pid) ?>">Edit</a>
              <form method="post" action="<?= htmlspecialchars($base) ?>/product/delete/<?= htmlspecialchars($pid) ?>" onsubmit="return confirm('Delete this product?');">
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
    <p class="empty-state">No products yet. Add your first product to get started.</p>
  <?php endif; ?>
</main>
</body>
</html>
