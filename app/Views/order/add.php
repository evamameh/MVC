<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <?php require __DIR__ . '/../head_assets.php'; ?>
  <title>Add order — InventoryCore</title>
</head>
<body class="app">
<main class="page-wrap">
  <header class="page-head">
    <h1 class="page-title">Add order</h1>
    <nav class="page-actions" aria-label="Section actions">
      <a class="btn btn-secondary" href="<?= htmlspecialchars($base) ?>/order">Back to list</a>
    </nav>
  </header>

  <?php if (!empty($errors)): ?>
    <div class="form-error"><?= implode('<br>', array_map(static fn ($e) => htmlspecialchars((string) $e), $errors)) ?></div>
  <?php endif; ?>

  <p class="notice">Choose <strong>Type</strong> and <strong>Category</strong> first; then pick a product (only that category).</p>

  <form id="orderForm" method="POST">
    <label>Type</label>
    <select name="type" id="orderType" required>
      <option value="sale" <?= (!isset($old['type']) || (string)($old['type'] ?? '') === 'sale' || (string)($old['type'] ?? '') === 'return') ? 'selected' : '' ?>>Sale</option>
      <option value="purchase" <?= (isset($old['type']) && (string)$old['type'] === 'purchase') ? 'selected' : '' ?>>Purchase</option>
    </select>

    <?php
    $categories = $categories ?? [];
    $preCat = '';
    if (!empty($old['product_id'])) {
        foreach ($products as $p) {
            if ((string) ($p['id'] ?? '') === (string) $old['product_id']) {
                $preCat = (string) ($p['category_id'] ?? '');
                break;
            }
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
            <?= isset($old['product_id']) && (string)$old['product_id']=== (string)$p['id'] ? 'selected' : '' ?>
          ><?= htmlspecialchars((string)$p['name']) ?> (Qty: <?= htmlspecialchars((string) $pStock) ?>)</option>
        <?php endforeach; ?>
      </select>
      <p id="orderProductStockHint" class="notice" hidden aria-live="polite">Product stock: <strong id="orderProductStockHintVal"></strong></p>

      <label for="orderQuantity">Quantity</label>
      <?php
      $qtyOld = trim((string) ($old['quantity'] ?? ''));
      $qtyInputValue = ($qtyOld !== '' && ctype_digit($qtyOld) && (int) $qtyOld > 0) ? $qtyOld : '1';
      ?>
      <input id="orderQuantity" name="quantity" type="number" min="1" value="<?= htmlspecialchars($qtyInputValue) ?>" />
    </div>

    <button type="submit">Save order</button>
  </form>
</main>
<script>
(() => {
  const typeEl = document.getElementById('orderType');
  const catEl = document.getElementById('orderCategoryFilter');
  const prodEl = document.getElementById('orderProduct');
  const qtyEl = document.getElementById('orderQuantity');
  const productBlock = document.getElementById('orderProductBlock');
  const stockHint = document.getElementById('orderProductStockHint');
  const stockHintVal = document.getElementById('orderProductStockHintVal');
  if (!typeEl || !catEl || !prodEl || !qtyEl || !productBlock) return;

  const pickable = (o) => o.value && !o.hidden;

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
      qtyEl.value = '1';
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
})();
</script>
</body>
</html>
