<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_admin();

$page_title = 'Dashboard';

// Stats
$total_users    = (int)$pdo->query('SELECT COUNT(*) FROM users WHERE role = "user"')->fetchColumn();
$total_products = (int)$pdo->query('SELECT COUNT(*) FROM products')->fetchColumn();
$total_orders   = (int)$pdo->query('SELECT COUNT(*) FROM orders')->fetchColumn();
$revenue        = (float)$pdo->query('SELECT COALESCE(SUM(total), 0) FROM orders WHERE status != "cancelled"')->fetchColumn();

// Recent orders
$recent = $pdo->query(
    'SELECT o.*, u.name AS user_name FROM orders o JOIN users u ON u.id = o.user_id ORDER BY o.created_at DESC LIMIT 10'
)->fetchAll();

require_once __DIR__ . '/includes/header.php';

$status_colors = [
    'pending'    => 'warning',
    'processing' => 'info',
    'shipped'    => 'primary',
    'delivered'  => 'success',
    'cancelled'  => 'danger',
];
?>

<!-- Stat cards -->
<div class="row g-3 mb-4">
  <div class="col-6 col-xl-3">
    <div class="card stat-card shadow-sm">
      <div class="card-body d-flex align-items-center gap-3">
        <div class="bg-primary bg-opacity-10 p-3 rounded-3">
          <i class="fas fa-users fa-2x text-primary"></i>
        </div>
        <div>
          <p class="text-muted small mb-0">Total Users</p>
          <h3 class="mb-0 fw-bold"><?= number_format($total_users) ?></h3>
        </div>
      </div>
    </div>
  </div>
  <div class="col-6 col-xl-3">
    <div class="card stat-card shadow-sm">
      <div class="card-body d-flex align-items-center gap-3">
        <div class="bg-success bg-opacity-10 p-3 rounded-3">
          <i class="fas fa-box fa-2x text-success"></i>
        </div>
        <div>
          <p class="text-muted small mb-0">Total Products</p>
          <h3 class="mb-0 fw-bold"><?= number_format($total_products) ?></h3>
        </div>
      </div>
    </div>
  </div>
  <div class="col-6 col-xl-3">
    <div class="card stat-card shadow-sm">
      <div class="card-body d-flex align-items-center gap-3">
        <div class="bg-warning bg-opacity-10 p-3 rounded-3">
          <i class="fas fa-shopping-bag fa-2x text-warning"></i>
        </div>
        <div>
          <p class="text-muted small mb-0">Total Orders</p>
          <h3 class="mb-0 fw-bold"><?= number_format($total_orders) ?></h3>
        </div>
      </div>
    </div>
  </div>
  <div class="col-6 col-xl-3">
    <div class="card stat-card shadow-sm">
      <div class="card-body d-flex align-items-center gap-3">
        <div class="bg-danger bg-opacity-10 p-3 rounded-3">
          <i class="fas fa-dollar-sign fa-2x text-danger"></i>
        </div>
        <div>
          <p class="text-muted small mb-0">Revenue</p>
          <h3 class="mb-0 fw-bold">$<?= number_format($revenue, 2) ?></h3>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Recent orders -->
<div class="card shadow-sm mb-4">
  <div class="card-header d-flex justify-content-between align-items-center">
    <h6 class="mb-0 fw-bold"><i class="fas fa-clock me-1"></i>Recent Orders</h6>
    <a href="/admin/orders.php" class="btn btn-sm btn-outline-primary">View All</a>
  </div>
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-hover mb-0 align-middle">
        <thead class="table-light">
          <tr>
            <th>Order #</th>
            <th>Customer</th>
            <th>Total</th>
            <th>Status</th>
            <th>Date</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($recent)): ?>
          <tr><td colspan="6" class="text-center text-muted py-3">No orders yet.</td></tr>
          <?php else: ?>
          <?php foreach ($recent as $order):
            $color = $status_colors[$order['status']] ?? 'secondary';
          ?>
          <tr>
            <td class="fw-bold">#<?= $order['id'] ?></td>
            <td><?= htmlspecialchars($order['user_name']) ?></td>
            <td>$<?= number_format($order['total'], 2) ?></td>
            <td><span class="badge bg-<?= $color ?>"><?= ucfirst(htmlspecialchars($order['status'])) ?></span></td>
            <td><?= date('M j, Y', strtotime($order['created_at'])) ?></td>
            <td><a href="/admin/orders.php?id=<?= $order['id'] ?>" class="btn btn-sm btn-outline-secondary"><i class="fas fa-eye"></i></a></td>
          </tr>
          <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
