<?php
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/auth.php';

require_login();

$errors = [];
$user_id = (int)$_SESSION['user_id'];

// Fetch cart items
$stmt = $pdo->prepare(
    'SELECT c.product_id, c.quantity, p.name, p.price, p.stock
     FROM cart c JOIN products p ON p.id = c.product_id
     WHERE c.user_id = ?'
);
$stmt->execute([$user_id]);
$cart_items = $stmt->fetchAll();

// Merge session cart into DB cart if any
if (!empty($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $pid => $item) {
        $s2 = $pdo->prepare('INSERT INTO cart (user_id, product_id, quantity) VALUES (?,?,?)
                              ON DUPLICATE KEY UPDATE quantity = quantity + VALUES(quantity)');
        $s2->execute([$user_id, $pid, $item['quantity']]);
    }
    unset($_SESSION['cart']);
    // Refresh cart
    $stmt->execute([$user_id]);
    $cart_items = $stmt->fetchAll();
}

if (empty($cart_items)) {
    header('Location: /cart.php');
    exit;
}

$subtotal = 0.0;
foreach ($cart_items as $item) {
    $subtotal += $item['price'] * $item['quantity'];
}
$shipping = $subtotal >= 50 ? 0 : 5.99;
$total    = $subtotal + $shipping;

// Pre-fill form from user data
$user_stmt = $pdo->prepare('SELECT name, email FROM users WHERE id = ?');
$user_stmt->execute([$user_id]);
$user = $user_stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    $name    = trim($_POST['name']    ?? '');
    $email   = trim($_POST['email']   ?? '');
    $address = trim($_POST['address'] ?? '');
    $city    = trim($_POST['city']    ?? '');
    $state   = trim($_POST['state']   ?? '');
    $zip     = trim($_POST['zip']     ?? '');

    if (!$name)    $errors[] = 'Full name is required.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Valid email is required.';
    if (!$address) $errors[] = 'Address is required.';
    if (!$city)    $errors[] = 'City is required.';
    if (!$state)   $errors[] = 'State is required.';
    if (!$zip)     $errors[] = 'ZIP code is required.';

    if (empty($errors)) {
        // Insert order
        $pdo->beginTransaction();
        try {
            $os = $pdo->prepare(
                'INSERT INTO orders (user_id, total, name, email, address, city, state, zip)
                 VALUES (?,?,?,?,?,?,?,?)'
            );
            $os->execute([$user_id, $total, $name, $email, $address, $city, $state, $zip]);
            $order_id = (int)$pdo->lastInsertId();

            foreach ($cart_items as $item) {
                $oi = $pdo->prepare('INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?,?,?,?)');
                $oi->execute([$order_id, $item['product_id'], $item['quantity'], $item['price']]);
                // Reduce stock
                $us = $pdo->prepare('UPDATE products SET stock = GREATEST(0, stock - ?) WHERE id = ?');
                $us->execute([$item['quantity'], $item['product_id']]);
            }

            // Clear cart
            $pdo->prepare('DELETE FROM cart WHERE user_id = ?')->execute([$user_id]);
            $pdo->commit();

            header("Location: /order-success.php?id={$order_id}");
            exit;
        } catch (Exception $e) {
            $pdo->rollBack();
            $errors[] = 'Order could not be placed. Please try again.';
        }
    }
} else {
    $name    = $user['name']  ?? '';
    $email   = $user['email'] ?? '';
    $address = $city = $state = $zip = '';
}

$page_title = 'Checkout';
require_once __DIR__ . '/includes/header.php';
?>

<div class="container py-5">
  <h1 class="mb-4"><i class="fas fa-credit-card me-2"></i>Checkout</h1>

  <?php if ($errors): ?>
  <div class="alert alert-danger">
    <ul class="mb-0">
      <?php foreach ($errors as $e): ?>
      <li><?= htmlspecialchars($e) ?></li>
      <?php endforeach; ?>
    </ul>
  </div>
  <?php endif; ?>

  <form method="POST" action="/checkout.php">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token()) ?>">
    <div class="row g-4">
      <!-- Shipping form -->
      <div class="col-lg-7">
        <div class="card shadow-sm">
          <div class="card-header bg-primary text-white fw-bold">Shipping Information</div>
          <div class="card-body">
            <div class="mb-3">
              <label class="form-label">Full Name *</label>
              <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($name) ?>" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Email *</label>
              <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($email) ?>" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Address *</label>
              <input type="text" name="address" class="form-control" value="<?= htmlspecialchars($address) ?>" required>
            </div>
            <div class="row">
              <div class="col-md-6 mb-3">
                <label class="form-label">City *</label>
                <input type="text" name="city" class="form-control" value="<?= htmlspecialchars($city) ?>" required>
              </div>
              <div class="col-md-3 mb-3">
                <label class="form-label">State *</label>
                <input type="text" name="state" class="form-control" value="<?= htmlspecialchars($state) ?>" required>
              </div>
              <div class="col-md-3 mb-3">
                <label class="form-label">ZIP *</label>
                <input type="text" name="zip" class="form-control" value="<?= htmlspecialchars($zip) ?>" required>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Order summary -->
      <div class="col-lg-5">
        <div class="card shadow-sm">
          <div class="card-header bg-dark text-white fw-bold">Order Summary</div>
          <div class="card-body p-0">
            <ul class="list-group list-group-flush">
              <?php foreach ($cart_items as $item): ?>
              <li class="list-group-item d-flex justify-content-between">
                <span><?= htmlspecialchars($item['name']) ?> <span class="text-muted">×<?= $item['quantity'] ?></span></span>
                <span>$<?= number_format($item['price'] * $item['quantity'], 2) ?></span>
              </li>
              <?php endforeach; ?>
              <li class="list-group-item d-flex justify-content-between">
                <span>Subtotal</span><span>$<?= number_format($subtotal, 2) ?></span>
              </li>
              <li class="list-group-item d-flex justify-content-between">
                <span>Shipping</span>
                <span class="<?= $shipping === 0 ? 'text-success' : '' ?>"><?= $shipping === 0 ? 'Free' : '$' . number_format($shipping, 2) ?></span>
              </li>
              <li class="list-group-item d-flex justify-content-between fw-bold fs-5">
                <span>Total</span><span>$<?= number_format($total, 2) ?></span>
              </li>
            </ul>
          </div>
          <div class="card-footer">
            <div class="d-grid">
              <button type="submit" class="btn btn-success btn-lg">
                <i class="fas fa-check-circle me-1"></i>Place Order
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </form>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
