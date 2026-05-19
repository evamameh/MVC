<?php
$orders = $orders ?? [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <?php require __DIR__ . '/../head_assets.php'; ?>
  <title>Orders — InventoryCore</title>
</head>
<body class="app">
<main class="page-wrap orders-list-page">
  <header class="page-head">
    <h1 class="page-title">Orders</h1>
    <nav class="page-actions" aria-label="Section actions">
      <a class="btn btn-primary" href="<?= htmlspecialchars($base) ?>/order/add">+ Add order</a>
      <a class="btn btn-secondary" href="<?= htmlspecialchars($base) ?>/dashboard">Dashboard</a>
    </nav>
  </header>

  <?php if (!empty($success)): ?><p class="flash flash--success"><?= htmlspecialchars((string)$success) ?></p><?php endif; ?>
  <?php if (!empty($error)): ?><p class="flash flash--error"><?= htmlspecialchars((string)$error) ?></p><?php endif; ?>

  <?php if ($orders !== []): ?>
  <div class="table-wrap">
    <table>
      <thead><tr><th>Type</th><th>Product name</th><th>Order qty</th><th>Stock</th><th>Unit price</th><th>Total price</th><th>Date</th><th>Actions</th></tr></thead>
      <tbody>
      <?php
      $orderTypeLabel = static function (array $o): string {
          $t = strtolower(trim((string) ($o['type'] ?? '')));

          return match ($t) {
              'purchase' => 'Purchase',
              'sale' => 'Sale',
              'return' => 'Return',
              default => $t !== '' ? $t : '—',
          };
      };
      ?>
      <?php foreach ($orders as $o): ?>
        <tr>
          <td><?= htmlspecialchars($orderTypeLabel($o)) ?></td>
          <td><?= htmlspecialchars((string)($o['product_name'] ?? 'Unknown product')) ?></td>
          <td><?= htmlspecialchars((string)($o['quantity'] ?? '')) ?></td>
          <td><?= htmlspecialchars((string)($o['product_qty'] ?? '0')) ?></td>
          <td><?= htmlspecialchars(number_format((float) ($o['unit_price'] ?? 0), 2, '.', ',')) ?></td>
          <td><?= htmlspecialchars(number_format((float) ($o['line_amount'] ?? 0), 2, '.', ',')) ?></td>
          <td><?= htmlspecialchars((string)($o['created_at'] ?? '')) ?></td>
          <td>
            <nav aria-label="Row actions">
              <a class="btn btn-sm btn-secondary" href="<?= htmlspecialchars($base) ?>/order/edit/<?= htmlspecialchars((string)$o['id']) ?>">Edit</a>
              <form method="post" action="<?= htmlspecialchars($base) ?>/order/delete/<?= htmlspecialchars((string)$o['id']) ?>" onsubmit="return confirm('Delete this order?');">
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
    <p class="empty-state">No orders yet. Add an order to get started.</p>
  <?php endif; ?>
</main>
</body>
</html>
