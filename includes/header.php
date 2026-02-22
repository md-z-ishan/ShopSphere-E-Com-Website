<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../includes/auth.php';

// Cart count
$cart_count = 0;
if (is_logged_in()) {
    require_once __DIR__ . '/../config/db.php';
    $stmt = $pdo->prepare('SELECT COALESCE(SUM(quantity), 0) AS cnt FROM cart WHERE user_id = ?');
    $stmt->execute([$_SESSION['user_id']]);
    $cart_count = (int)$stmt->fetchColumn();
} else {
    if (!empty($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as $item) {
            $cart_count += (int)$item['quantity'];
        }
    }
}

$page_title = isset($page_title) ? htmlspecialchars($page_title) . ' — ShopSphere' : 'ShopSphere';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= $page_title ?></title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    body { padding-top: 70px; }
    .navbar-brand { font-weight: 700; font-size: 1.5rem; }
    .cart-badge { font-size: .65rem; }
    .product-card img { height: 200px; object-fit: cover; }
    .hero-section { background: linear-gradient(135deg, #0d6efd 0%, #6610f2 100%); color: #fff; padding: 80px 0; }
    .category-card:hover { transform: translateY(-4px); transition: transform .2s; }
    footer { background: #212529; color: #adb5bd; }
  </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top shadow">
  <div class="container">
    <a class="navbar-brand" href="/index.php">
      <i class="fas fa-store me-1"></i>ShopSphere
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav"
            aria-controls="mainNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="mainNav">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item"><a class="nav-link" href="/index.php"><i class="fas fa-home me-1"></i>Home</a></li>
        <li class="nav-item"><a class="nav-link" href="/products.php"><i class="fas fa-th-large me-1"></i>Products</a></li>
        <?php if (is_logged_in()): ?>
        <li class="nav-item"><a class="nav-link" href="/orders.php"><i class="fas fa-box me-1"></i>My Orders</a></li>
        <?php if (is_admin()): ?>
        <li class="nav-item"><a class="nav-link" href="/admin/index.php"><i class="fas fa-cog me-1"></i>Admin</a></li>
        <?php endif; ?>
        <?php endif; ?>
      </ul>
      <ul class="navbar-nav ms-auto mb-2 mb-lg-0 align-items-center">
        <li class="nav-item me-2">
          <a class="nav-link position-relative" href="/cart.php">
            <i class="fas fa-shopping-cart fa-lg"></i>
            <span id="cart-count-badge"
                  class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger cart-badge"
                  <?= $cart_count === 0 ? 'style="display:none"' : '' ?>>
              <?= $cart_count ?>
            </span>
          </a>
        </li>
        <?php if (is_logged_in()): ?>
        <li class="nav-item">
          <span class="nav-link text-light">
            <i class="fas fa-user me-1"></i><?= htmlspecialchars($_SESSION['user_name'] ?? 'Account') ?>
          </span>
        </li>
        <li class="nav-item">
          <a class="nav-link btn btn-outline-light btn-sm px-3" href="/logout.php">
            <i class="fas fa-sign-out-alt me-1"></i>Logout
          </a>
        </li>
        <?php else: ?>
        <li class="nav-item"><a class="nav-link" href="/login.php"><i class="fas fa-sign-in-alt me-1"></i>Login</a></li>
        <li class="nav-item">
          <a class="btn btn-primary btn-sm ms-1" href="/register.php"><i class="fas fa-user-plus me-1"></i>Register</a>
        </li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>
