<?php
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/auth.php';

require_login();

$order_id = (int)($_GET['id'] ?? 0);
if ($order_id <= 0) {
    header('Location: /index.php');
    exit;
}

// Ensure the order belongs to the logged-in user
$stmt = $pdo->prepare('SELECT * FROM orders WHERE id = ? AND user_id = ?');
$stmt->execute([$order_id, $_SESSION['user_id']]);
$order = $stmt->fetch();

if (!$order) {
    http_response_code(404);
    die('Order not found.');
}

$items_stmt = $pdo->prepare(
    'SELECT oi.*, p.name AS product_name FROM order_items oi
     JOIN products p ON p.id = oi.product_id
     WHERE oi.order_id = ?'
);
$items_stmt->execute([$order_id]);
$items = $items_stmt->fetchAll();

$page_title = 'Order Confirmed';
require_once __DIR__ . '/includes/header.php';
?>

<div class="container py-5">
  <div class="text-center mb-5">
    <div class="mb-3">
      <i class="fas fa-check-circle text-success" style="font-size:5rem"></i>
    </div>
    <h1 class="fw-bold text-success">Order Placed Successfully!</h1>
    <p class="text-muted lead">Thank you for shopping with ShopSphere. Your order has been received.</p>
    <span class="badge bg-primary fs-5">Order #<?= $order_id ?></span>
  </div>

  <div class="row justify-content-center">
    <div class="col-md-8">
      <div class="card shadow mb-4">
        <div class="card-header bg-primary text-white fw-bold">
          <i class="fas fa-receipt me-1"></i>Order Details
        </div>
        <div class="card-body">
          <div class="row mb-3">
            <div class="col-sm-6">
              <p class="mb-1"><strong>Order ID:</strong> #<?= $order_id ?></p>
              <p class="mb-1"><strong>Date:</strong> <?= date('F j, Y', strtotime($order['created_at'])) ?></p>
              <p class="mb-0">
                <strong>Status:</strong>
                <span class="badge bg-warning text-dark"><?= ucfirst(htmlspecialchars($order['status'])) ?></span>
              </p>
            </div>
            <div class="col-sm-6">
              <p class="mb-1"><strong>Ship To:</strong> <?= htmlspecialchars($order['name']) ?></p>
              <p class="mb-1"><?= htmlspecialchars($order['address']) ?></p>
              <p class="mb-0"><?= htmlspecialchars($order['city']) ?>, <?= htmlspecialchars($order['state']) ?> <?= htmlspecialchars($order['zip']) ?></p>
            </div>
          </div>

          <table class="table table-bordered table-sm">
            <thead class="table-light">
              <tr>
                <th>Product</th>
                <th class="text-center">Qty</th>
                <th class="text-end">Price</th>
                <th class="text-end">Subtotal</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($items as $item): ?>
              <tr>
                <td><?= htmlspecialchars($item['product_name']) ?></td>
                <td class="text-center"><?= $item['quantity'] ?></td>
                <td class="text-end">$<?= number_format($item['price'], 2) ?></td>
                <td class="text-end">$<?= number_format($item['price'] * $item['quantity'], 2) ?></td>
              </tr>
              <?php endforeach; ?>
            </tbody>
            <tfoot>
              <tr>
                <td colspan="3" class="text-end fw-bold">Total</td>
                <td class="text-end fw-bold text-primary">$<?= number_format($order['total'], 2) ?></td>
              </tr>
            </tfoot>
          </table>
        </div>
      </div>

      <div class="text-center d-flex gap-3 justify-content-center">
        <a href="/orders.php" class="btn btn-outline-primary"><i class="fas fa-box me-1"></i>My Orders</a>
        <a href="/products.php" class="btn btn-primary"><i class="fas fa-shopping-bag me-1"></i>Continue Shopping</a>
      </div>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
