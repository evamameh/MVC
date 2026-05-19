<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <?php require __DIR__ . '/head_assets.php'; ?>
  <title>Dashboard — InventoryCore</title>
</head>
<body class="app dashboard">
  <div class="dash-page">
    <a class="dash-logout" href="<?= htmlspecialchars($base) ?>/logout">Sign out</a>

    <header class="dash-header">
      <h1>InventoryCore</h1>
      <p class="dash-sub">Pick a section to manage your inventory.</p>
    </header>

    <?php if (!empty($success)): ?>
      <p class="dash-flash"><?= htmlspecialchars((string) $success) ?></p>
    <?php endif; ?>

    <nav class="dash-grid" aria-label="Main sections">
      <a class="dash-card" href="<?= htmlspecialchars($base) ?>/products">
        <span class="dash-card-icon" aria-hidden="true">📦</span>
        <span class="dash-card-title">Products</span>
        <span class="dash-card-desc">Browse, add, and edit items and prices.</span>
      </a>
      <a class="dash-card" href="<?= htmlspecialchars($base) ?>/category">
        <span class="dash-card-icon" aria-hidden="true">🏷️</span>
        <span class="dash-card-title">Categories</span>
        <span class="dash-card-desc">Group products so filters and reports stay organized.</span>
      </a>
      <a class="dash-card" href="<?= htmlspecialchars($base) ?>/supplier">
        <span class="dash-card-icon" aria-hidden="true">🚚</span>
        <span class="dash-card-title">Suppliers</span>
        <span class="dash-card-desc">Keep vendor names and contacts linked to your catalog.</span>
      </a>
      <a class="dash-card" href="<?= htmlspecialchars($base) ?>/order">
        <span class="dash-card-icon" aria-hidden="true">📋</span>
        <span class="dash-card-title">Orders</span>
        <span class="dash-card-desc">Record purchases, sales, and customer returns.</span>
      </a>
      <a class="dash-card" href="<?= htmlspecialchars($base) ?>/userlist">
        <span class="dash-card-icon" aria-hidden="true">👤</span>
        <span class="dash-card-title">Users</span>
        <span class="dash-card-desc">Manage who can sign in and use the dashboard.</span>
      </a>
    </nav>
  </div>
</body>
</html>
