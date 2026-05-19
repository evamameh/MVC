<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <?php require __DIR__ . '/../head_assets.php'; ?>
  <title>Add new user — InventoryCore</title>
</head>
<body class="app user-admin-page">
<main class="page-wrap">
  <p class="user-admin-breadcrumb">Organization <span aria-hidden="true">›</span> User management</p>
  <header class="user-admin-page-head">
    <div class="user-admin-page-head__titles">
      <h1 class="page-title">Add new user</h1>
      <p class="user-admin-page-head__subtitle">Create an account with login and password.</p>
    </div>
    <nav class="page-actions" aria-label="Section actions">
      <a class="btn btn-secondary" href="<?= htmlspecialchars($base) ?>/userlist">Back to list</a>
    </nav>
  </header>

  <?php if (!empty($error)): ?><div class="form-error"><?= htmlspecialchars((string) $error) ?></div><?php endif; ?>

  <section class="user-admin-card user-admin-card--narrow">
    <form method="POST" action="<?= htmlspecialchars($base) ?>/userlist/create" class="user-create-form">
      <label for="cu-username">Username</label>
      <input id="cu-username" name="username" required autocomplete="username" />

      <label for="cu-password">Password</label>
      <input id="cu-password" name="password" type="password" required autocomplete="new-password" minlength="3" />
      <p class="user-drawer__hint">At least 3 characters.</p>

      <div class="user-drawer__actions">
        <button type="submit" class="btn btn-primary">Save user</button>
        <a class="btn btn-secondary" href="<?= htmlspecialchars($base) ?>/userlist">Cancel</a>
      </div>
    </form>
  </section>
</main>
</body>
</html>
