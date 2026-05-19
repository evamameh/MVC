<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <?php require __DIR__ . '/../head_assets.php'; ?>
  <title>Edit user — InventoryCore</title>
</head>
<body class="app user-admin-page">
<main class="page-wrap">
  <header class="user-admin-page-head">
    <div class="user-admin-page-head__titles">
      <h1 class="page-title">Edit user</h1>
      <p class="user-admin-page-head__subtitle">Change username or set a new password.</p>
    </div>
    <nav class="page-actions" aria-label="Section actions">
      <a class="btn btn-secondary" href="<?= htmlspecialchars($base) ?>/userlist">Back to list</a>
    </nav>
  </header>

  <?php if (!empty($error)): ?><div class="form-error"><?= htmlspecialchars((string) $error) ?></div><?php endif; ?>

  <section class="user-admin-card user-admin-card--narrow">
    <form method="POST" action="<?= htmlspecialchars($base) ?>/user/edit/<?= htmlspecialchars((string) ($user['id'] ?? '')) ?>" class="user-create-form">
      <label for="eu-username">Username</label>
      <input id="eu-username" name="username" required autocomplete="username" value="<?= htmlspecialchars((string) ($user['username'] ?? '')) ?>" />

      <label for="eu-password">New password <small>(optional)</small></label>
      <input id="eu-password" name="password" type="password" autocomplete="new-password" minlength="3" />
      <p class="user-drawer__hint">Leave blank to keep the current password. At least 3 characters if you change it.</p>

      <div class="user-drawer__actions">
        <button type="submit" class="btn btn-primary">Save changes</button>
        <a class="btn btn-secondary" href="<?= htmlspecialchars($base) ?>/userlist">Cancel</a>
      </div>
    </form>
  </section>
</main>
</body>
</html>
