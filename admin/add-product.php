<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_admin();

$errors = [];

// Utility: generate slug
function make_slug(string $str): string {
    $str = strtolower(trim($str));
    $str = preg_replace('/[^a-z0-9\s-]/', '', $str);
    return preg_replace('/[\s-]+/', '-', $str);
}

$categories = $pdo->query('SELECT * FROM categories ORDER BY name')->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    $name        = trim($_POST['name']        ?? '');
    $category_id = (int)($_POST['category_id'] ?? 0);
    $description = trim($_POST['description'] ?? '');
    $price       = (float)($_POST['price']    ?? 0);
    $stock       = (int)($_POST['stock']      ?? 0);
    $image       = trim($_POST['image']       ?? '');
    $featured    = isset($_POST['featured']) ? 1 : 0;

    if (!$name)           $errors[] = 'Product name is required.';
    if ($category_id <= 0) $errors[] = 'Please select a category.';
    if ($price <= 0)      $errors[] = 'Price must be greater than 0.';
    if ($stock < 0)       $errors[] = 'Stock cannot be negative.';

    if (empty($errors)) {
        $slug = make_slug($name);
        // Ensure unique slug
        $check = $pdo->prepare('SELECT id FROM products WHERE slug = ?');
        $check->execute([$slug]);
        if ($check->fetch()) {
            $slug .= '-' . time();
        }

        $stmt = $pdo->prepare(
            'INSERT INTO products (category_id, name, slug, description, price, stock, image, featured)
             VALUES (?,?,?,?,?,?,?,?)'
        );
        $stmt->execute([$category_id, $name, $slug, $description, $price, $stock, $image ?: null, $featured]);
        header('Location: /admin/products.php?saved=1');
        exit;
    }
}

$page_title = 'Add Product';
require_once __DIR__ . '/includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-3">
  <h5 class="mb-0">Add New Product</h5>
  <a href="/admin/products.php" class="btn btn-outline-secondary btn-sm"><i class="fas fa-arrow-left me-1"></i>Back</a>
</div>

<?php if ($errors): ?>
<div class="alert alert-danger">
  <ul class="mb-0"><?php foreach ($errors as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?></ul>
</div>
<?php endif; ?>

<div class="card shadow-sm">
  <div class="card-body">
    <form method="POST" action="/admin/add-product.php">
      <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token()) ?>">
      <div class="row g-3">
        <div class="col-md-8">
          <label class="form-label">Product Name *</label>
          <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" required>
        </div>
        <div class="col-md-4">
          <label class="form-label">Category *</label>
          <select name="category_id" class="form-select" required>
            <option value="">Select category...</option>
            <?php foreach ($categories as $cat): ?>
            <option value="<?= $cat['id'] ?>" <?= (int)($_POST['category_id'] ?? 0) === (int)$cat['id'] ? 'selected' : '' ?>>
              <?= htmlspecialchars($cat['name']) ?>
            </option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-12">
          <label class="form-label">Description</label>
          <textarea name="description" class="form-control" rows="4"><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
        </div>
        <div class="col-md-4">
          <label class="form-label">Price ($) *</label>
          <input type="number" name="price" class="form-control" step="0.01" min="0.01"
                 value="<?= htmlspecialchars($_POST['price'] ?? '') ?>" required>
        </div>
        <div class="col-md-4">
          <label class="form-label">Stock *</label>
          <input type="number" name="stock" class="form-control" min="0"
                 value="<?= htmlspecialchars($_POST['stock'] ?? '0') ?>" required>
        </div>
        <div class="col-md-4">
          <label class="form-label">Image URL</label>
          <input type="url" name="image" class="form-control" placeholder="https://..."
                 value="<?= htmlspecialchars($_POST['image'] ?? '') ?>">
        </div>
        <div class="col-12">
          <div class="form-check">
            <input class="form-check-input" type="checkbox" name="featured" id="featured"
                   <?= isset($_POST['featured']) ? 'checked' : '' ?>>
            <label class="form-check-label" for="featured">Featured product (show on homepage)</label>
          </div>
        </div>
        <div class="col-12 d-flex gap-2">
          <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i>Save Product</button>
          <a href="/admin/products.php" class="btn btn-outline-secondary">Cancel</a>
        </div>
      </div>
    </form>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
