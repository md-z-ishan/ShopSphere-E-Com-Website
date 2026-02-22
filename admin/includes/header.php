<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../../includes/auth.php';
require_admin();

$page_title = isset($page_title) ? htmlspecialchars($page_title) . ' — Admin' : 'Admin Panel';
$current    = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= $page_title ?> | ShopSphere</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    body { background: #f0f2f5; }
    .sidebar { min-height: 100vh; background: #1e293b; }
    .sidebar .nav-link { color: #94a3b8; padding: .6rem 1.2rem; border-radius: 6px; margin: 2px 8px; }
    .sidebar .nav-link:hover, .sidebar .nav-link.active { color: #fff; background: #334155; }
    .sidebar .nav-link i { width: 20px; }
    .sidebar .brand { color: #fff; font-weight: 700; font-size: 1.3rem; padding: 1rem 1.2rem; border-bottom: 1px solid #334155; }
    .main-content { min-height: 100vh; }
    .stat-card { border: none; border-radius: 12px; }
    .topbar { background: #fff; border-bottom: 1px solid #e2e8f0; }
  </style>
</head>
<body>
<div class="d-flex">
  <!-- Sidebar -->
  <nav class="sidebar d-flex flex-column" style="width:240px; min-width:240px;">
    <div class="brand"><i class="fas fa-store me-2"></i>ShopSphere</div>
    <ul class="nav flex-column mt-2 flex-grow-1">
      <li class="nav-item">
        <a class="nav-link <?= $current === 'index.php' ? 'active' : '' ?>" href="/admin/index.php">
          <i class="fas fa-tachometer-alt me-2"></i>Dashboard
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link <?= $current === 'products.php' || $current === 'add-product.php' || $current === 'edit-product.php' ? 'active' : '' ?>" href="/admin/products.php">
          <i class="fas fa-box me-2"></i>Products
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link <?= $current === 'orders.php' ? 'active' : '' ?>" href="/admin/orders.php">
          <i class="fas fa-shopping-bag me-2"></i>Orders
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link <?= $current === 'users.php' ? 'active' : '' ?>" href="/admin/users.php">
          <i class="fas fa-users me-2"></i>Users
        </a>
      </li>
      <li class="nav-item mt-auto">
        <a class="nav-link" href="/index.php"><i class="fas fa-globe me-2"></i>View Site</a>
      </li>
      <li class="nav-item">
        <a class="nav-link text-danger" href="/logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a>
      </li>
    </ul>
  </nav>

  <!-- Main content -->
  <div class="flex-grow-1 main-content">
    <div class="topbar d-flex align-items-center justify-content-between px-4 py-2 mb-4">
      <h5 class="mb-0 fw-bold"><?= $page_title ?></h5>
      <div class="text-muted small">
        <i class="fas fa-user-shield me-1"></i>
        <?= htmlspecialchars($_SESSION['user_name'] ?? 'Admin') ?>
      </div>
    </div>
    <div class="px-4">
