<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <?php require __DIR__ . '/../head_assets.php'; ?>
  <title>Edit order — InventoryCore</title>
</head>
<body class="app">
<main class="page-wrap">
  <header class="page-head">
    <h1 class="page-title">Edit order</h1>
    <nav class="page-actions" aria-label="Section actions">
      <a class="btn btn-secondary" href="<?= htmlspecialchars($base) ?>/order">Back to list</a>
    </nav>
  </header>

  <?php if (!empty($errors)): ?>
    <div class="form-error"><?= implode('<br>', array_map(static fn ($e) => htmlspecialchars((string) $e), $errors)) ?></div>
  <?php endif; ?>

  <p class="notice">Choose <strong>Category</strong> to see products (that category only).</p>

  <form id="editForm" method="POST">
    <label>Type</label>
    <select name="type" id="orderType" required>
      <option value="sale" <?= (($order['type'] ?? '') === 'sale') ? 'selected' : '' ?>>Sale</option>
      <option value="purchase" <?= (($order['type'] ?? '') === 'purchase') ? 'selected' : '' ?>>Purchase</option>
      <option value="return" <?= (($order['type'] ?? '') === 'return') ? 'selected' : '' ?>>Return</option>
    </select>

    <?php
    $categories = $categories ?? [];
    $preCat = '';
    foreach ($products as $p) {
        if ((string) ($p['id'] ?? '') === (string) ($order['product_id'] ?? '')) {
            $preCat = (string) ($p['category_id'] ?? '');
            break;
        }
    }
    ?>
    <label for="orderCategoryFilter">Category</label>
    <select id="orderCategoryFilter" required aria-controls="orderProductBlock" title="Choose a category to see products">
      <option value="" disabled <?= $preCat === '' ? 'selected' : '' ?>>Select category</option>
      <?php foreach ($categories as $cat): ?>
        <?php $cid = (string) ($cat['id'] ?? ''); ?>
        <option value="<?= htmlspecialchars($cid) ?>" <?= $preCat === $cid ? 'selected' : '' ?>><?= htmlspecialchars((string) ($cat['name'] ?? '')) ?></option>
      <?php endforeach; ?>
    </select>

    <div id="orderProductBlock" <?= $preCat === '' ? 'hidden' : '' ?>>
      <label for="orderProduct">Product</label>
      <select name="product_id" id="orderProduct">
        <option value="">Select product</option>
        <?php foreach ($products as $p): ?>
          <?php $pStock = (int) ($p['quantity'] ?? 0); ?>
          <option
            value="<?= htmlspecialchars((string)$p['id']) ?>"
            data-category-id="<?= htmlspecialchars((string)($p['category_id'] ?? '')) ?>"
            data-stock="<?= htmlspecialchars((string) $pStock) ?>"
            <?= (string)($order['product_id'] ?? '') === (string)$p['id'] ? 'selected' : '' ?>
          ><?= htmlspecialchars((string)$p['name']) ?> (Qty: <?= htmlspecialchars((string) $pStock) ?>)</option>
        <?php endforeach; ?>
      </select>
      <p id="orderProductStockHint" class="notice" hidden aria-live="polite">Product stock: <strong id="orderProductStockHintVal"></strong></p>

      <label for="orderQuantity">Quantity</label>
      <input id="orderQuantity" name="quantity" type="number" min="1" value="<?= htmlspecialchars((string)($order['quantity'] ?? '')) ?>" />
    </div>

    <button id="updateBtn" type="submit">Save changes</button>
  </form>
</main>
<script>
(() => {
  const typeEl = document.getElementById('orderType');
  const catEl = document.getElementById('orderCategoryFilter');
  const prodEl = document.getElementById('orderProduct');
  const qtyEl = document.getElementById('orderQuantity');
  const productBlock = document.getElementById('orderProductBlock');

  if (typeEl && catEl && prodEl && qtyEl && productBlock) {
    const pickable = (o) => o.value && !o.hidden;

    const stockHint = document.getElementById('orderProductStockHint');
    const stockHintVal = document.getElementById('orderProductStockHintVal');

    const syncStockHint = () => {
      const opt = prodEl.selectedOptions[0];
      if (!stockHint || !stockHintVal) return;
      if (!opt || !opt.value) {
        stockHint.hidden = true;
        return;
      }
      const n = parseInt(opt.getAttribute('data-stock') || '0', 10);
      stockHintVal.textContent = String(Number.isFinite(n) ? n : 0);
      stockHint.hidden = false;
    };

    const setProductBlock = (show) => {
      productBlock.hidden = !show;
      prodEl.required = show;
      qtyEl.required = show;
      if (!show) {
        prodEl.value = '';
        syncStockHint();
      }
    };

    const applyCategoryFilter = () => {
      const cat = catEl.value;
      const showBlock = cat !== '';
      setProductBlock(showBlock);
      if (!showBlock) {
        return;
      }
      const opts = Array.from(prodEl.options);
      let selectedOk = false;
      for (const opt of opts) {
        if (!opt.value) {
          opt.hidden = false;
          continue;
        }
        const cid = opt.getAttribute('data-category-id') || '';
        opt.hidden = cid !== cat;
        if (opt.selected && pickable(opt)) {
          selectedOk = true;
        }
      }
      if (!selectedOk) {
        const first = opts.find(pickable);
        prodEl.value = first ? first.value : '';
        if (first) {
          prodEl.dispatchEvent(new Event('change', { bubbles: true }));
        }
      }
      syncStockHint();
    };

    typeEl.addEventListener('change', applyCategoryFilter);
    catEl.addEventListener('change', applyCategoryFilter);
    prodEl.addEventListener('change', () => {
      qtyEl.value = '1';
      syncStockHint();
    });
    applyCategoryFilter();
  }

  const form = document.getElementById('editForm');
  const updateBtn = document.getElementById('updateBtn');
  if (form && updateBtn) {
    const initial = JSON.stringify(Object.fromEntries(new FormData(form).entries()));
    const syncSave = () => {
      updateBtn.disabled = JSON.stringify(Object.fromEntries(new FormData(form).entries())) === initial;
    };
    form.addEventListener('input', syncSave);
    form.addEventListener('change', syncSave);
    syncSave();
  }
})();
</script>
</body>
</html>
