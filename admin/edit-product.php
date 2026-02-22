<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_admin();

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    header('Location: /admin/products.php');
    exit;
}

$stmt = $pdo->prepare('SELECT * FROM products WHERE id = ?');
$stmt->execute([$id]);
$product = $stmt->fetch();
if (!$product) {
    header('Location: /admin/products.php');
    exit;
}

function make_slug(string $str): string {
    $str = strtolower(trim($str));
    $str = preg_replace('/[^a-z0-9\s-]/', '', $str);
    return preg_replace('/[\s-]+/', '-', $str);
}

$categories = $pdo->query('SELECT * FROM categories ORDER BY name')->fetchAll();
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    $name        = trim($_POST['name']        ?? '');
    $category_id = (int)($_POST['category_id'] ?? 0);
    $description = trim($_POST['description'] ?? '');
    $price       = (float)($_POST['price']    ?? 0);
    $stock       = (int)($_POST['stock']      ?? 0);
    $image       = trim($_POST['image']       ?? '');
    $featured    = isset($_POST['featured']) ? 1 : 0;

    if (!$name)            $errors[] = 'Product name is required.';
    if ($category_id <= 0) $errors[] = 'Please select a category.';
    if ($price <= 0)       $errors[] = 'Price must be greater than 0.';
    if ($stock < 0)        $errors[] = 'Stock cannot be negative.';

    if (empty($errors)) {
        $new_slug = make_slug($name);
        // Ensure unique slug (excluding current product)
        $chk = $pdo->prepare('SELECT id FROM products WHERE slug = ? AND id != ?');
        $chk->execute([$new_slug, $id]);
        if ($chk->fetch()) {
            $new_slug .= '-' . time();
        }

        $upd = $pdo->prepare(
            'UPDATE products SET category_id=?, name=?, slug=?, description=?, price=?, stock=?, image=?, featured=?
             WHERE id=?'
        );
        $upd->execute([$category_id, $name, $new_slug, $description, $price, $stock, $image ?: null, $featured, $id]);
        header('Location: /admin/products.php?saved=1');
        exit;
    }

    // Re-populate from POST on error
    $product = array_merge($product, [
        'name'        => $name,
        'category_id' => $category_id,
        'description' => $description,
        'price'       => $price,
        'stock'       => $stock,
        'image'       => $image,
        'featured'    => $featured,
    ]);
}

$page_title = 'Edit Product';
require_once __DIR__ . '/includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-3">
  <h5 class="mb-0">Edit Product: <?= htmlspecialchars($product['name']) ?></h5>
  <a href="/admin/products.php" class="btn btn-outline-secondary btn-sm"><i class="fas fa-arrow-left me-1"></i>Back</a>
</div>

<?php if ($errors): ?>
<div class="alert alert-danger">
  <ul class="mb-0"><?php foreach ($errors as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?></ul>
</div>
<?php endif; ?>

<div class="card shadow-sm">
  <div class="card-body">
    <form method="POST" action="/admin/edit-product.php?id=<?= $id ?>">
      <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token()) ?>">
      <div class="row g-3">
        <div class="col-md-8">
          <label class="form-label">Product Name *</label>
          <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($product['name']) ?>" required>
        </div>
        <div class="col-md-4">
          <label class="form-label">Category *</label>
          <select name="category_id" class="form-select" required>
            <option value="">Select category...</option>
            <?php foreach ($categories as $cat): ?>
            <option value="<?= $cat['id'] ?>" <?= (int)$product['category_id'] === (int)$cat['id'] ? 'selected' : '' ?>>
              <?= htmlspecialchars($cat['name']) ?>
            </option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-12">
          <label class="form-label">Description</label>
          <textarea name="description" class="form-control" rows="4"><?= htmlspecialchars($product['description'] ?? '') ?></textarea>
        </div>
        <div class="col-md-4">
          <label class="form-label">Price ($) *</label>
          <input type="number" name="price" class="form-control" step="0.01" min="0.01"
                 value="<?= htmlspecialchars($product['price']) ?>" required>
        </div>
        <div class="col-md-4">
          <label class="form-label">Stock *</label>
          <input type="number" name="stock" class="form-control" min="0"
                 value="<?= htmlspecialchars($product['stock']) ?>" required>
        </div>
        <div class="col-md-4">
          <label class="form-label">Image URL</label>
          <input type="url" name="image" class="form-control" placeholder="https://..."
                 value="<?= htmlspecialchars($product['image'] ?? '') ?>">
        </div>
        <div class="col-12">
          <div class="form-check">
            <input class="form-check-input" type="checkbox" name="featured" id="featured"
                   <?= $product['featured'] ? 'checked' : '' ?>>
            <label class="form-check-label" for="featured">Featured product (show on homepage)</label>
          </div>
        </div>
        <div class="col-12 d-flex gap-2">
          <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i>Update Product</button>
          <a href="/admin/products.php" class="btn btn-outline-secondary">Cancel</a>
        </div>
      </div>
    </form>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
