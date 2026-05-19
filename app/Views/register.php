<?php
$base = (string) ($base ?? '');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <?php require __DIR__ . '/head_assets.php'; ?>
  <title>InventoryCore — Register</title>
</head>
<body class="app">
  <main class="page-wrap page-wrap--auth">
    <div class="auth-card">
      <h1 class="page-title">Create account</h1>
      <p class="auth-lead">Join InventoryCore to get started.</p>
      <?php if (!empty($error)): ?>
        <p class="flash flash--error"><?= htmlspecialchars((string)$error) ?></p>
      <?php endif; ?>
      <form method="POST" action="<?= htmlspecialchars($base) ?>/register">
        <label for="username">Username</label>
        <input id="username" name="username" type="text" autocomplete="username" required value="<?= htmlspecialchars((string) ($username ?? '')) ?>" />
        <label for="password">Password <small>(at least 3 characters)</small></label>
        <input id="password" name="password" type="password" autocomplete="new-password" required minlength="3" />
        <div class="form-actions">
          <button type="submit">Register</button>
          <a class="btn btn-ghost" href="<?= htmlspecialchars($base !== '' ? $base . '/login' : '/login') ?>">Cancel</a>
        </div>
      </form>
      <p class="form-foot">Already registered? <a href="<?= htmlspecialchars($base) ?>/login">Sign in</a></p>
    </div>
  </main>
</body>
</html>
