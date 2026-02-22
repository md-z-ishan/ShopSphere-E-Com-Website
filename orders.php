<?php
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/auth.php';

require_login();

$user_id = (int)$_SESSION['user_id'];
$stmt = $pdo->prepare(
    'SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC'
);
$stmt->execute([$user_id]);
$orders = $stmt->fetchAll();

$page_title = 'My Orders';
require_once __DIR__ . '/includes/header.php';

$status_colors = [
    'pending'    => 'warning',
    'processing' => 'info',
    'shipped'    => 'primary',
    'delivered'  => 'success',
    'cancelled'  => 'danger',
];
?>

<div class="container py-5">
  <h1 class="mb-4"><i class="fas fa-box me-2"></i>My Orders</h1>

  <?php if (empty($orders)): ?>
  <div class="text-center py-5">
    <i class="fas fa-box-open fa-5x text-muted mb-3"></i>
    <h3 class="text-muted">No orders yet</h3>
    <p class="text-muted">You haven't placed any orders.</p>
    <a href="/products.php" class="btn btn-primary mt-2"><i class="fas fa-shopping-bag me-1"></i>Start Shopping</a>
  </div>
  <?php else: ?>
  <div class="card shadow-sm">
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-hover mb-0 align-middle">
          <thead class="table-dark">
            <tr>
              <th>Order #</th>
              <th>Date</th>
              <th>Total</th>
              <th>Status</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($orders as $order):
              $color = $status_colors[$order['status']] ?? 'secondary';
            ?>
            <tr>
              <td class="fw-bold">#<?= $order['id'] ?></td>
              <td><?= htmlspecialchars(date('M j, Y', strtotime($order['created_at']))) ?></td>
              <td class="fw-semibold text-primary">$<?= number_format($order['total'], 2) ?></td>
              <td><span class="badge bg-<?= $color ?>"><?= ucfirst(htmlspecialchars($order['status'])) ?></span></td>
              <td>
                <a href="/order-success.php?id=<?= $order['id'] ?>" class="btn btn-sm btn-outline-primary">
                  <i class="fas fa-eye me-1"></i>View
                </a>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
  <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
