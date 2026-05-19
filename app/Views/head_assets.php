<?php





$b = rtrim((string) ($base ?? ''), '/');
if (str_ends_with($b, '/index.php')) {
    $b = rtrim(substr($b, 0, -strlen('/index.php')), '/');
}
?>
<meta name="viewport" content="width=device-width, initial-scale=1" />
<link rel="stylesheet" href="<?= htmlspecialchars($b) ?>/css/app.css" />
