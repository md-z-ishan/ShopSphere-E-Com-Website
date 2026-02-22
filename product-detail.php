<?php
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/auth.php';

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    header('Location: /products.php');
    exit;
}

$stmt = $pdo->prepare(
    'SELECT p.*, c.name AS category_name, c.slug AS category_slug
     FROM products p JOIN categories c ON c.id = p.category_id
     WHERE p.id = ?'
);
$stmt->execute([$id]);
$product = $stmt->fetch();

if (!$product) {
    http_response_code(404);
    die('Product not found.');
}

$page_title = $product['name'];
require_once __DIR__ . '/includes/header.php';

$img = $product['image'] ?: 'https://via.placeholder.com/600x400?text=' . urlencode($product['name']);
?>

<div class="container py-5">
  <!-- Breadcrumb -->
  <nav aria-label="breadcrumb" class="mb-4">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="/index.php">Home</a></li>
      <li class="breadcrumb-item"><a href="/products.php">Products</a></li>
      <li class="breadcrumb-item">
        <a href="/products.php?category=<?= htmlspecialchars($product['category_slug']) ?>">
          <?= htmlspecialchars($product['category_name']) ?>
        </a>
      </li>
      <li class="breadcrumb-item active"><?= htmlspecialchars($product['name']) ?></li>
    </ol>
  </nav>

  <div class="row g-5">
    <!-- Product image -->
    <div class="col-md-5">
      <img src="<?= htmlspecialchars($img) ?>" class="img-fluid rounded shadow" alt="<?= htmlspecialchars($product['name']) ?>">
    </div>

    <!-- Product info -->
    <div class="col-md-7">
      <span class="badge bg-secondary mb-2"><?= htmlspecialchars($product['category_name']) ?></span>
      <h1 class="h2 fw-bold"><?= htmlspecialchars($product['name']) ?></h1>
      <h2 class="text-primary fw-bold mb-3">$<?= number_format($product['price'], 2) ?></h2>

      <?php if ($product['stock'] > 0): ?>
      <span class="badge bg-success fs-6 mb-3"><i class="fas fa-check-circle me-1"></i>In Stock (<?= (int)$product['stock'] ?> available)</span>
      <?php else: ?>
      <span class="badge bg-danger fs-6 mb-3"><i class="fas fa-times-circle me-1"></i>Out of Stock</span>
      <?php endif; ?>

      <p class="text-muted"><?= nl2br(htmlspecialchars($product['description'] ?? '')) ?></p>

      <?php if ($product['stock'] > 0): ?>
      <div class="d-flex align-items-center gap-3 mb-4">
        <label class="fw-semibold mb-0">Quantity:</label>
        <div class="input-group" style="width:140px">
          <button class="btn btn-outline-secondary" type="button" id="qty-minus">−</button>
          <input type="number" id="product-qty" class="form-control text-center" value="1" min="1" max="<?= min(10, $product['stock']) ?>">
          <button class="btn btn-outline-secondary" type="button" id="qty-plus">+</button>
        </div>
      </div>
      <div class="d-flex gap-2">
        <button id="btn-add-cart" class="btn btn-primary btn-lg" data-id="<?= $product['id'] ?>">
          <i class="fas fa-cart-plus me-1"></i>Add to Cart
        </button>
        <a href="/cart.php" class="btn btn-outline-secondary btn-lg">
          <i class="fas fa-shopping-cart me-1"></i>View Cart
        </a>
      </div>
      <?php endif; ?>

      <div id="cart-message" class="mt-3"></div>
    </div>
  </div>
</div>

<div id="toast-container" class="position-fixed bottom-0 end-0 p-3" style="z-index:9999"></div>

<script>
$(function () {
    var max = <?= min(10, $product['stock']) ?>;

    $('#qty-minus').on('click', function () {
        var v = parseInt($('#product-qty').val(), 10);
        if (v > 1) $('#product-qty').val(v - 1);
    });
    $('#qty-plus').on('click', function () {
        var v = parseInt($('#product-qty').val(), 10);
        if (v < max) $('#product-qty').val(v + 1);
    });
    $('#product-qty').on('change', function () {
        var v = parseInt($(this).val(), 10);
        if (isNaN(v) || v < 1) v = 1;
        if (v > max) v = max;
        $(this).val(v);
    });

    $('#btn-add-cart').on('click', function () {
        var btn = $(this);
        var qty = parseInt($('#product-qty').val(), 10);
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i>Adding...');
        $.post('/ajax/cart.php', {
            action: 'add',
            product_id: btn.data('id'),
            quantity: qty
        }, function (res) {
            if (res.success) {
                showToast('Item added to cart!', 'success');
                updateCartBadge();
            } else {
                showToast(res.message || 'Could not add item.', 'danger');
            }
        }, 'json').always(function () {
            btn.prop('disabled', false).html('<i class="fas fa-cart-plus me-1"></i>Add to Cart');
        });
    });

    function showToast(msg, type) {
        var id = 'toast-' + Date.now();
        $('#toast-container').append(
            '<div id="' + id + '" class="toast align-items-center text-bg-' + type + ' border-0 show" role="alert">'
            + '<div class="d-flex"><div class="toast-body">' + msg + '</div>'
            + '<button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>'
            + '</div></div>'
        );
        setTimeout(function () { $('#' + id).remove(); }, 3000);
    }
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
