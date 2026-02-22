<?php
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/auth.php';

$page_title = 'Home';
require_once __DIR__ . '/includes/header.php';

// Featured products
$stmt = $pdo->query('SELECT p.*, c.name AS category_name FROM products p JOIN categories c ON c.id = p.category_id WHERE p.featured = 1 ORDER BY p.created_at DESC LIMIT 8');
$featured = $stmt->fetchAll();

// Categories
$cats = $pdo->query('SELECT * FROM categories ORDER BY name')->fetchAll();
?>

<!-- Hero Section -->
<section class="hero-section text-center">
  <div class="container">
    <h1 class="display-4 fw-bold mb-3"><i class="fas fa-store me-2"></i>Welcome to ShopSphere</h1>
    <p class="lead mb-4">Discover amazing products at unbeatable prices. Electronics, Clothing, Books and more!</p>
    <a href="/products.php" class="btn btn-light btn-lg me-2"><i class="fas fa-shopping-bag me-1"></i>Shop Now</a>
    <a href="/register.php" class="btn btn-outline-light btn-lg"><i class="fas fa-user-plus me-1"></i>Join Free</a>
  </div>
</section>

<!-- Category Section -->
<section class="py-5 bg-light">
  <div class="container">
    <h2 class="text-center fw-bold mb-4">Shop by Category</h2>
    <div class="row g-3 justify-content-center">
      <?php
      $icons = ['electronics' => 'fa-laptop', 'clothing' => 'fa-tshirt', 'books' => 'fa-book'];
      $colors = ['electronics' => 'primary', 'clothing' => 'success', 'books' => 'warning'];
      foreach ($cats as $cat):
        $icon  = $icons[$cat['slug']]  ?? 'fa-tag';
        $color = $colors[$cat['slug']] ?? 'secondary';
      ?>
      <div class="col-6 col-md-3">
        <a href="/products.php?category=<?= htmlspecialchars($cat['slug']) ?>" class="text-decoration-none">
          <div class="card text-center shadow-sm category-card h-100">
            <div class="card-body py-4">
              <div class="mb-2">
                <i class="fas <?= $icon ?> fa-3x text-<?= $color ?>"></i>
              </div>
              <h5 class="mb-0"><?= htmlspecialchars($cat['name']) ?></h5>
            </div>
          </div>
        </a>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- Featured Products -->
<section class="py-5">
  <div class="container">
    <h2 class="text-center fw-bold mb-4"><i class="fas fa-star text-warning me-2"></i>Featured Products</h2>
    <?php if (empty($featured)): ?>
    <p class="text-center text-muted">No featured products yet.</p>
    <?php else: ?>
    <div class="row g-4">
      <?php foreach ($featured as $product):
        $img = $product['image'] ?: 'https://via.placeholder.com/300x200?text=' . urlencode($product['name']);
      ?>
      <div class="col-6 col-md-4 col-lg-3">
        <div class="card h-100 shadow-sm product-card">
          <a href="/product-detail.php?id=<?= $product['id'] ?>">
            <img src="<?= htmlspecialchars($img) ?>" class="card-img-top" alt="<?= htmlspecialchars($product['name']) ?>">
          </a>
          <div class="card-body d-flex flex-column">
            <span class="badge bg-secondary mb-1 align-self-start"><?= htmlspecialchars($product['category_name']) ?></span>
            <h6 class="card-title mt-1"><?= htmlspecialchars($product['name']) ?></h6>
            <p class="text-primary fw-bold mt-auto mb-2">$<?= number_format($product['price'], 2) ?></p>
            <div class="d-grid gap-1">
              <button class="btn btn-primary btn-sm btn-add-cart" data-id="<?= $product['id'] ?>">
                <i class="fas fa-cart-plus me-1"></i>Add to Cart
              </button>
              <a href="/product-detail.php?id=<?= $product['id'] ?>" class="btn btn-outline-secondary btn-sm">View Details</a>
            </div>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
    <div class="text-center mt-4">
      <a href="/products.php" class="btn btn-outline-primary btn-lg">View All Products <i class="fas fa-arrow-right ms-1"></i></a>
    </div>
  </div>
</section>

<!-- Trust Badges -->
<section class="py-4 bg-light border-top border-bottom">
  <div class="container">
    <div class="row text-center g-3">
      <div class="col-6 col-md-3">
        <i class="fas fa-shipping-fast fa-2x text-primary mb-2"></i>
        <p class="small fw-semibold mb-0">Free Shipping</p>
        <p class="small text-muted">On orders over $50</p>
      </div>
      <div class="col-6 col-md-3">
        <i class="fas fa-lock fa-2x text-success mb-2"></i>
        <p class="small fw-semibold mb-0">Secure Payment</p>
        <p class="small text-muted">100% protected</p>
      </div>
      <div class="col-6 col-md-3">
        <i class="fas fa-undo fa-2x text-warning mb-2"></i>
        <p class="small fw-semibold mb-0">Easy Returns</p>
        <p class="small text-muted">30-day return policy</p>
      </div>
      <div class="col-6 col-md-3">
        <i class="fas fa-headset fa-2x text-danger mb-2"></i>
        <p class="small fw-semibold mb-0">24/7 Support</p>
        <p class="small text-muted">Always here to help</p>
      </div>
    </div>
  </div>
</section>

<div id="toast-container" class="position-fixed bottom-0 end-0 p-3" style="z-index:9999"></div>

<script>
$(function () {
    $(document).on('click', '.btn-add-cart', function () {
        var btn = $(this);
        var productId = btn.data('id');
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i>Adding...');
        $.post('/ajax/cart.php', { action: 'add', product_id: productId, quantity: 1 }, function (res) {
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
        var html = '<div id="' + id + '" class="toast align-items-center text-bg-' + type + ' border-0 show" role="alert">'
                 + '<div class="d-flex"><div class="toast-body">' + msg + '</div>'
                 + '<button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>'
                 + '</div></div>';
        $('#toast-container').append(html);
        setTimeout(function () { $('#' + id).remove(); }, 3000);
    }
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
