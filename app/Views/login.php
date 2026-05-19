<?php
$base = (string) ($base ?? '');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <?php require __DIR__ . '/head_assets.php'; ?>
  <title>InventoryCore — Login</title>
</head>
<body class="app">
  <main class="page-wrap page-wrap--auth">
    <div class="auth-card">
      <h1 class="page-title">InventoryCore</h1>
      <p class="auth-lead">Sign in to manage inventory.</p>
      <?php if (!empty($success)): ?>
        <p class="flash flash--success"><?= htmlspecialchars((string) $success) ?></p>
      <?php endif; ?>
      <?php if (!empty($error)): ?>
        <p class="flash flash--error"><?= htmlspecialchars((string) $error) ?></p>
      <?php endif; ?>
      <form method="POST" action="<?= htmlspecialchars($base) ?>/login">
        <label for="username">Username</label>
        <input id="username" name="username" type="text" autocomplete="username" required />
        <label for="password">Password</label>
        <input id="password" name="password" type="password" autocomplete="current-password" required />
        <div class="form-actions">
          <button type="submit">Sign in</button>
          <a class="btn btn-ghost" href="<?= htmlspecialchars($base) ?>/login">Cancel</a>
        </div>
      </form>
      <p class="form-foot">Don't have an account? <a href="<?= htmlspecialchars($base) ?>/register">Register</a></p>
    </div>
  </main>
</body>
</html>
