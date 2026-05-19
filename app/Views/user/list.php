<?php
$users = $users ?? [];
$totalUsers = (int) ($totalUsers ?? 0);
$page = max(1, (int) ($page ?? 1));
$totalPages = max(1, (int) ($totalPages ?? 1));

$userInitials = static function (array $u): string {
    $uName = (string) ($u['username'] ?? '');

    return strtoupper(mb_substr($uName, 0, 1, 'UTF-8') . mb_substr($uName, 1, 1, 'UTF-8'));
};
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <?php require __DIR__ . '/../head_assets.php'; ?>
  <title>System users — InventoryCore</title>
</head>
<body class="app user-admin-page">
<main class="page-wrap">
  <header class="user-admin-page-head">
    <div class="user-admin-page-head__titles">
      <h1 class="page-title">System users</h1>
    </div>
    <nav class="page-actions" aria-label="Section actions">
      <a class="btn btn-primary" href="<?= htmlspecialchars($base) ?>/userlist/create">+ Add new user</a>
      <a class="btn btn-secondary" href="<?= htmlspecialchars($base) ?>/dashboard">Dashboard</a>
    </nav>
  </header>

  <?php if (!empty($success)): ?><p class="flash flash--success"><?= htmlspecialchars((string) $success) ?></p><?php endif; ?>
  <?php if (!empty($error)): ?><p class="flash flash--error"><?= htmlspecialchars((string) $error) ?></p><?php endif; ?>

  <section class="user-admin-card" aria-labelledby="user-card-title">
    <div class="user-admin-card__head">
      <h2 id="user-card-title" class="user-admin-card__title">All registered users</h2>
      <span class="user-admin-card__badge">Total: <?= (int) $totalUsers ?></span>
    </div>

    <?php if ($totalUsers > 0): ?>
    <div class="table-wrap table-wrap--rounded">
      <table class="data-table data-table--users">
        <thead>
          <tr>
            <th scope="col">User</th>
            <th scope="col">Username</th>
            <th scope="col">Password</th>
            <th scope="col">Actions</th>
          </tr>
        </thead>
        <tbody>
        <?php foreach ($users as $user): ?>
          <?php $uid = (string) ($user['id'] ?? ''); ?>
          <tr>
            <td>
              <div class="user-cell">
                <span class="user-avatar" aria-hidden="true"><?= htmlspecialchars($userInitials($user)) ?></span>
                <span class="user-cell__name"><?= htmlspecialchars((string) ($user['username'] ?? '')) ?></span>
              </div>
            </td>
            <td class="mono"><?= htmlspecialchars((string) ($user['username'] ?? '')) ?></td>
            <td><span class="user-password-mask">••••••••</span></td>
            <td>
              <div class="user-icon-actions">
                <a class="user-icon-btn user-icon-btn--edit" href="<?= htmlspecialchars($base) ?>/user/edit/<?= htmlspecialchars($uid) ?>" title="Edit user">Edit</a>
                <form method="post" action="<?= htmlspecialchars($base) ?>/userlist/delete/<?= htmlspecialchars($uid) ?>" onsubmit="return confirm('Delete this user?');">
                  <button class="user-icon-btn user-icon-btn--delete" type="submit" title="Delete user">Delete</button>
                </form>
              </div>
            </td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>

    <?php if ($totalPages > 1): ?>
    <footer class="user-admin-table-foot">
      <nav class="pagination-bar" aria-label="User pages">
        <div class="pagination-bar__links">
          <?php
          $buildPageUrl = static function (int $p) use ($base): string {
              return $p > 1 ? $base . '/userlist?page=' . $p : $base . '/userlist';
          };
          ?>
          <?php if ($page > 1): ?>
            <a class="btn btn-sm btn-secondary" href="<?= htmlspecialchars($buildPageUrl($page - 1)) ?>">Previous</a>
          <?php endif; ?>
          <?php for ($pi = 1; $pi <= $totalPages; $pi++): ?>
            <?php if ($pi === $page): ?>
              <span class="pagination-bar__current" aria-current="page"><?= $pi ?></span>
            <?php else: ?>
              <a class="btn btn-sm btn-secondary" href="<?= htmlspecialchars($buildPageUrl($pi)) ?>"><?= $pi ?></a>
            <?php endif; ?>
          <?php endfor; ?>
          <?php if ($page < $totalPages): ?>
            <a class="btn btn-sm btn-secondary" href="<?= htmlspecialchars($buildPageUrl($page + 1)) ?>">Next</a>
          <?php endif; ?>
        </div>
      </nav>
    </footer>
    <?php endif; ?>

    <?php else: ?>
      <p class="empty-state user-admin-card__empty">No users yet. Add a user to grant access.</p>
    <?php endif; ?>
  </section>
</main>
</body>
</html>
