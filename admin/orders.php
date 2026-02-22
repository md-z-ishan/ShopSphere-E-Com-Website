<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_admin();

$success = '';
$error   = '';

// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $order_id  = (int)($_POST['order_id']  ?? 0);
    $new_status = $_POST['status'] ?? '';
    $allowed    = ['pending', 'processing', 'shipped', 'delivered', 'cancelled'];

    if ($order_id > 0 && in_array($new_status, $allowed, true)) {
        $pdo->prepare('UPDATE orders SET status = ? WHERE id = ?')->execute([$new_status, $order_id]);
        $success = "Order #{$order_id} status updated to " . ucfirst($new_status) . '.';
    } else {
        $error = 'Invalid request.';
    }
}

// If viewing a single order
$view_id = (int)($_GET['id'] ?? 0);
$view_order = null;
$view_items = [];
if ($view_id > 0) {
    $s = $pdo->prepare('SELECT o.*, u.name AS user_name FROM orders o JOIN users u ON u.id = o.user_id WHERE o.id = ?');
    $s->execute([$view_id]);
    $view_order = $s->fetch();
    if ($view_order) {
        $si = $pdo->prepare('SELECT oi.*, p.name AS product_name FROM order_items oi JOIN products p ON p.id = oi.product_id WHERE oi.order_id = ?');
        $si->execute([$view_id]);
        $view_items = $si->fetchAll();
    }
}

$orders = $pdo->query(
    'SELECT o.*, u.name AS user_name FROM orders o JOIN users u ON u.id = o.user_id ORDER BY o.created_at DESC'
)->fetchAll();

$status_colors = [
    'pending'    => 'warning',
    'processing' => 'info',
    'shipped'    => 'primary',
    'delivered'  => 'success',
    'cancelled'  => 'danger',
];
$statuses = ['pending', 'processing', 'shipped', 'delivered', 'cancelled'];

$page_title = 'Orders';
require_once __DIR__ . '/includes/header.php';
?>

<?php if ($success): ?>
<div class="alert alert-success alert-dismissible fade show"><i class="fas fa-check me-1"></i><?= htmlspecialchars($success) ?> <button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
<?php endif; ?>
<?php if ($error): ?>
<div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<?php if ($view_order): ?>
<!-- Single order detail -->
<div class="d-flex justify-content-between mb-3">
  <h5 class="mb-0">Order #<?= $view_order['id'] ?> Details</h5>
  <a href="/admin/orders.php" class="btn btn-outline-secondary btn-sm"><i class="fas fa-arrow-left me-1"></i>All Orders</a>
</div>
<div class="row g-3 mb-4">
  <div class="col-md-6">
    <div class="card shadow-sm">
      <div class="card-header fw-bold">Customer Info</div>
      <div class="card-body">
        <p><strong>Name:</strong> <?= htmlspecialchars($view_order['name']) ?></p>
        <p><strong>Email:</strong> <?= htmlspecialchars($view_order['email']) ?></p>
        <p><strong>Address:</strong> <?= htmlspecialchars($view_order['address']) ?>, <?= htmlspecialchars($view_order['city']) ?>, <?= htmlspecialchars($view_order['state']) ?> <?= htmlspecialchars($view_order['zip']) ?></p>
        <p class="mb-0"><strong>Ordered:</strong> <?= date('M j, Y H:i', strtotime($view_order['created_at'])) ?></p>
      </div>
    </div>
  </div>
  <div class="col-md-6">
    <div class="card shadow-sm">
      <div class="card-header fw-bold">Update Status</div>
      <div class="card-body">
        <form method="POST" action="/admin/orders.php?id=<?= $view_order['id'] ?>">
          <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token()) ?>">
          <input type="hidden" name="order_id" value="<?= $view_order['id'] ?>">
          <div class="input-group">
            <select name="status" class="form-select">
              <?php foreach ($statuses as $s): ?>
              <option value="<?= $s ?>" <?= $view_order['status'] === $s ? 'selected' : '' ?>><?= ucfirst($s) ?></option>
              <?php endforeach; ?>
            </select>
            <button type="submit" class="btn btn-primary">Update</button>
          </div>
        </form>
        <p class="mt-2 mb-0">
          <strong>Total:</strong> <span class="text-primary fw-bold">$<?= number_format($view_order['total'], 2) ?></span>
        </p>
      </div>
    </div>
  </div>
</div>
<div class="card shadow-sm mb-4">
  <div class="card-header fw-bold">Order Items</div>
  <div class="card-body p-0">
    <table class="table mb-0">
      <thead class="table-light"><tr><th>Product</th><th>Qty</th><th>Price</th><th>Subtotal</th></tr></thead>
      <tbody>
        <?php foreach ($view_items as $item): ?>
        <tr>
          <td><?= htmlspecialchars($item['product_name']) ?></td>
          <td><?= $item['quantity'] ?></td>
          <td>$<?= number_format($item['price'], 2) ?></td>
          <td>$<?= number_format($item['price'] * $item['quantity'], 2) ?></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
<?php else: ?>

<!-- All orders -->
<div class="d-flex justify-content-between align-items-center mb-3">
  <h5 class="mb-0">All Orders (<?= count($orders) ?>)</h5>
</div>
<div class="card shadow-sm">
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-hover mb-0 align-middle">
        <thead class="table-dark">
          <tr>
            <th>Order #</th>
            <th>Customer</th>
            <th>Total</th>
            <th>Status</th>
            <th>Date</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($orders)): ?>
          <tr><td colspan="6" class="text-center text-muted py-3">No orders yet.</td></tr>
          <?php else: ?>
          <?php foreach ($orders as $order):
            $color = $status_colors[$order['status']] ?? 'secondary';
          ?>
          <tr>
            <td class="fw-bold">#<?= $order['id'] ?></td>
            <td><?= htmlspecialchars($order['user_name']) ?></td>
            <td>$<?= number_format($order['total'], 2) ?></td>
            <td>
              <form method="POST" action="/admin/orders.php" class="d-flex gap-1" style="min-width:200px">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token()) ?>">
                <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                <select name="status" class="form-select form-select-sm">
                  <?php foreach ($statuses as $s): ?>
                  <option value="<?= $s ?>" <?= $order['status'] === $s ? 'selected' : '' ?>><?= ucfirst($s) ?></option>
                  <?php endforeach; ?>
                </select>
                <button type="submit" class="btn btn-sm btn-outline-primary">Save</button>
              </form>
            </td>
            <td><?= date('M j, Y', strtotime($order['created_at'])) ?></td>
            <td>
              <a href="/admin/orders.php?id=<?= $order['id'] ?>" class="btn btn-sm btn-outline-secondary">
                <i class="fas fa-eye"></i>
              </a>
            </td>
          </tr>
          <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
<?php endif; ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
