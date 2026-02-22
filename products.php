<?php
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/auth.php';

$page_title = 'Products';

// Filters
$search   = trim($_GET['search'] ?? '');
$cat_slug = trim($_GET['category'] ?? '');
$page_num = max(1, (int)($_GET['page'] ?? 1));
$per_page = 8;
$offset   = ($page_num - 1) * $per_page;

// Build query
$where  = ['1=1'];
$params = [];

if ($search !== '') {
    $where[]  = '(p.name LIKE ? OR p.description LIKE ?)';
    $params[] = "%{$search}%";
    $params[] = "%{$search}%";
}

$category = null;
if ($cat_slug !== '') {
    $stmt = $pdo->prepare('SELECT * FROM categories WHERE slug = ?');
    $stmt->execute([$cat_slug]);
    $category = $stmt->fetch();
    if ($category) {
        $where[]  = 'p.category_id = ?';
        $params[] = $category['id'];
    }
}

$whereSQL = implode(' AND ', $where);

// Count total
$count_params = $params;
$count_stmt   = $pdo->prepare("SELECT COUNT(*) FROM products p WHERE {$whereSQL}");
$count_stmt->execute($count_params);
$total_products = (int)$count_stmt->fetchColumn();
$total_pages    = (int)ceil($total_products / $per_page);

// Fetch products
$stmt = $pdo->prepare(
    "SELECT p.*, c.name AS category_name FROM products p
     JOIN categories c ON c.id = p.category_id
     WHERE {$whereSQL}
     ORDER BY p.created_at DESC
     LIMIT :limit OFFSET :offset"
);
// Bind all filter params positionally then bind LIMIT/OFFSET by name
foreach ($params as $i => $val) {
    $stmt->bindValue($i + 1, $val);
}
$stmt->bindValue(':limit',  $per_page, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset,   PDO::PARAM_INT);
$stmt->execute();
$products = $stmt->fetchAll();

// All categories for sidebar
$categories = $pdo->query('SELECT * FROM categories ORDER BY name')->fetchAll();

require_once __DIR__ . '/includes/header.php';
?>

<div class="container py-4">
  <!-- Search bar -->
  <form method="GET" action="/products.php" class="mb-4">
    <?php if ($cat_slug): ?><input type="hidden" name="category" value="<?= htmlspecialchars($cat_slug) ?>"><?php endif; ?>
    <div class="input-group">
      <input type="text" name="search" class="form-control form-control-lg"
             placeholder="Search products..." value="<?= htmlspecialchars($search) ?>">
      <button class="btn btn-primary" type="submit"><i class="fas fa-search me-1"></i>Search</button>
      <?php if ($search || $cat_slug): ?>
      <a href="/products.php" class="btn btn-outline-secondary">Clear</a>
      <?php endif; ?>
    </div>
  </form>

  <div class="row">
    <!-- Sidebar -->
    <aside class="col-md-3 mb-4">
      <div class="card shadow-sm">
        <div class="card-header bg-primary text-white fw-bold">
          <i class="fas fa-filter me-1"></i>Categories
        </div>
        <ul class="list-group list-group-flush">
          <li class="list-group-item <?= !$cat_slug ? 'active' : '' ?>">
            <a href="/products.php<?= $search ? '?search=' . urlencode($search) : '' ?>"
               class="text-decoration-none <?= !$cat_slug ? 'text-white' : '' ?>">
              <i class="fas fa-th me-1"></i>All Products
              <span class="badge bg-secondary float-end"><?= $total_products ?></span>
            </a>
          </li>
          <?php foreach ($categories as $cat): ?>
          <li class="list-group-item <?= $cat_slug === $cat['slug'] ? 'active' : '' ?>">
            <a href="/products.php?category=<?= htmlspecialchars($cat['slug']) ?><?= $search ? '&search=' . urlencode($search) : '' ?>"
               class="text-decoration-none <?= $cat_slug === $cat['slug'] ? 'text-white' : '' ?>">
              <?= htmlspecialchars($cat['name']) ?>
            </a>
          </li>
          <?php endforeach; ?>
        </ul>
      </div>
    </aside>

    <!-- Product grid -->
    <main class="col-md-9">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="mb-0">
          <?php if ($category): ?>
            <?= htmlspecialchars($category['name']) ?>
          <?php elseif ($search): ?>
            Search: "<?= htmlspecialchars($search) ?>"
          <?php else: ?>
            All Products
          <?php endif; ?>
          <small class="text-muted">(<?= $total_products ?> found)</small>
        </h5>
      </div>

      <?php if (empty($products)): ?>
      <div class="alert alert-info"><i class="fas fa-info-circle me-1"></i>No products found.</div>
      <?php else: ?>
      <div class="row g-3">
        <?php foreach ($products as $product):
          $img = $product['image'] ?: 'https://via.placeholder.com/300x200?text=' . urlencode($product['name']);
        ?>
        <div class="col-6 col-lg-4">
          <div class="card h-100 shadow-sm product-card">
            <a href="/product-detail.php?id=<?= $product['id'] ?>">
              <img src="<?= htmlspecialchars($img) ?>" class="card-img-top" alt="<?= htmlspecialchars($product['name']) ?>">
            </a>
            <div class="card-body d-flex flex-column">
              <span class="badge bg-secondary mb-1 align-self-start"><?= htmlspecialchars($product['category_name']) ?></span>
              <h6 class="card-title"><?= htmlspecialchars($product['name']) ?></h6>
              <p class="text-muted small flex-grow-1"><?= htmlspecialchars(mb_substr($product['description'] ?? '', 0, 70)) ?>...</p>
              <p class="text-primary fw-bold mb-2">$<?= number_format($product['price'], 2) ?></p>
              <?php if ($product['stock'] > 0): ?>
              <span class="badge bg-success mb-2">In Stock</span>
              <?php else: ?>
              <span class="badge bg-danger mb-2">Out of Stock</span>
              <?php endif; ?>
              <div class="d-grid gap-1">
                <button class="btn btn-primary btn-sm btn-add-cart" data-id="<?= $product['id'] ?>"
                  <?= $product['stock'] <= 0 ? 'disabled' : '' ?>>
                  <i class="fas fa-cart-plus me-1"></i>Add to Cart
                </button>
                <a href="/product-detail.php?id=<?= $product['id'] ?>" class="btn btn-outline-secondary btn-sm">View Details</a>
              </div>
            </div>
          </div>
        </div>
        <?php endforeach; ?>
      </div>

      <!-- Pagination -->
      <?php if ($total_pages > 1): ?>
      <nav class="mt-4" aria-label="Product pages">
        <ul class="pagination justify-content-center">
          <?php for ($i = 1; $i <= $total_pages; $i++):
            $q = http_build_query(array_filter(['search' => $search, 'category' => $cat_slug, 'page' => $i]));
          ?>
          <li class="page-item <?= $i === $page_num ? 'active' : '' ?>">
            <a class="page-link" href="/products.php?<?= $q ?>"><?= $i ?></a>
          </li>
          <?php endfor; ?>
        </ul>
      </nav>
      <?php endif; ?>
      <?php endif; ?>
    </main>
  </div>
</div>

<div id="toast-container" class="position-fixed bottom-0 end-0 p-3" style="z-index:9999"></div>

<script>
$(function () {
    $(document).on('click', '.btn-add-cart', function () {
        var btn = $(this);
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i>Adding...');
        $.post('/ajax/cart.php', { action: 'add', product_id: btn.data('id'), quantity: 1 }, function (res) {
            if (res.success) {
                showToast('Item added to cart!', 'success');
                updateCartBadge();
            } else {
                showToast(res.message || 'Error.', 'danger');
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
