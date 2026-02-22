<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_admin();

// Handle delete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'delete') {
    verify_csrf();
    $del_id = (int)($_POST['product_id'] ?? 0);
    if ($del_id > 0) {
        $pdo->prepare('DELETE FROM products WHERE id = ?')->execute([$del_id]);
    }
    header('Location: /admin/products.php?deleted=1');
    exit;
}

// Handle featured toggle
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'toggle_featured') {
    verify_csrf();
    $pid = (int)($_POST['product_id'] ?? 0);
    if ($pid > 0) {
        $pdo->prepare('UPDATE products SET featured = NOT featured WHERE id = ?')->execute([$pid]);
    }
    header('Location: /admin/products.php');
    exit;
}

$products = $pdo->query(
    'SELECT p.*, c.name AS category_name FROM products p JOIN categories c ON c.id = p.category_id ORDER BY p.created_at DESC'
)->fetchAll();

$page_title = 'Products';
require_once __DIR__ . '/includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-3">
  <h5 class="mb-0">All Products (<?= count($products) ?>)</h5>
  <a href="/admin/add-product.php" class="btn btn-primary"><i class="fas fa-plus me-1"></i>Add Product</a>
</div>

<?php if (isset($_GET['deleted'])): ?>
<div class="alert alert-success alert-dismissible fade show"><i class="fas fa-check me-1"></i>Product deleted. <button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
<?php endif; ?>
<?php if (isset($_GET['saved'])): ?>
<div class="alert alert-success alert-dismissible fade show"><i class="fas fa-check me-1"></i>Product saved successfully. <button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
<?php endif; ?>

<div class="card shadow-sm">
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-hover mb-0 align-middle">
        <thead class="table-dark">
          <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Category</th>
            <th>Price</th>
            <th>Stock</th>
            <th>Featured</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($products)): ?>
          <tr><td colspan="7" class="text-center text-muted py-3">No products yet.</td></tr>
          <?php else: ?>
          <?php foreach ($products as $p): ?>
          <tr>
            <td><?= $p['id'] ?></td>
            <td class="fw-semibold"><?= htmlspecialchars($p['name']) ?></td>
            <td><?= htmlspecialchars($p['category_name']) ?></td>
            <td>$<?= number_format($p['price'], 2) ?></td>
            <td>
              <?php if ($p['stock'] > 0): ?>
              <span class="badge bg-success"><?= $p['stock'] ?></span>
              <?php else: ?>
              <span class="badge bg-danger">Out</span>
              <?php endif; ?>
            </td>
            <td>
              <form method="POST" action="/admin/products.php" class="d-inline">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token()) ?>">
                <input type="hidden" name="action" value="toggle_featured">
                <input type="hidden" name="product_id" value="<?= $p['id'] ?>">
                <button type="submit" class="btn btn-sm <?= $p['featured'] ? 'btn-warning' : 'btn-outline-secondary' ?>">
                  <i class="fas fa-star"></i>
                </button>
              </form>
            </td>
            <td>
              <a href="/admin/edit-product.php?id=<?= $p['id'] ?>" class="btn btn-sm btn-outline-primary me-1">
                <i class="fas fa-edit"></i>
              </a>
              <form method="POST" action="/admin/products.php" class="d-inline"
                    onsubmit="return confirm('Delete this product?')">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token()) ?>">
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="product_id" value="<?= $p['id'] ?>">
                <button type="submit" class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
              </form>
            </td>
          </tr>
          <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
