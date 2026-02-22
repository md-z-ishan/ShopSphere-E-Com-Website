<?php
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/auth.php';

$page_title = 'Shopping Cart';

// Fetch cart items (DB for logged-in, session for guests)
$cart_items = [];
$total      = 0.0;

if (is_logged_in()) {
    $stmt = $pdo->prepare(
        'SELECT c.product_id, c.quantity, p.name, p.price, p.stock, p.image, p.slug
         FROM cart c JOIN products p ON p.id = c.product_id
         WHERE c.user_id = ?'
    );
    $stmt->execute([$_SESSION['user_id']]);
    $cart_items = $stmt->fetchAll();
} else {
    if (!empty($_SESSION['cart'])) {
        $ids = array_keys($_SESSION['cart']);
        if ($ids) {
            $placeholders = implode(',', array_fill(0, count($ids), '?'));
            $stmt = $pdo->prepare("SELECT * FROM products WHERE id IN ({$placeholders})");
            $stmt->execute($ids);
            $products_map = [];
            foreach ($stmt->fetchAll() as $p) {
                $products_map[$p['id']] = $p;
            }
            foreach ($_SESSION['cart'] as $pid => $item) {
                if (isset($products_map[$pid])) {
                    $p = $products_map[$pid];
                    $cart_items[] = [
                        'product_id' => $pid,
                        'quantity'   => $item['quantity'],
                        'name'       => $p['name'],
                        'price'      => $p['price'],
                        'stock'      => $p['stock'],
                        'image'      => $p['image'],
                        'slug'       => $p['slug'],
                    ];
                }
            }
        }
    }
}

foreach ($cart_items as $item) {
    $total += $item['price'] * $item['quantity'];
}

require_once __DIR__ . '/includes/header.php';
?>

<div class="container py-5">
  <h1 class="mb-4"><i class="fas fa-shopping-cart me-2"></i>Shopping Cart</h1>

  <?php if (empty($cart_items)): ?>
  <div class="text-center py-5">
    <i class="fas fa-cart-arrow-down fa-5x text-muted mb-3"></i>
    <h3 class="text-muted">Your cart is empty</h3>
    <p class="text-muted">Looks like you haven't added anything yet.</p>
    <a href="/products.php" class="btn btn-primary btn-lg mt-2"><i class="fas fa-shopping-bag me-1"></i>Start Shopping</a>
  </div>
  <?php else: ?>
  <div class="row g-4">
    <!-- Cart table -->
    <div class="col-lg-8">
      <div class="card shadow-sm">
        <div class="card-body p-0">
          <table class="table mb-0 align-middle">
            <thead class="table-light">
              <tr>
                <th>Product</th>
                <th>Price</th>
                <th>Quantity</th>
                <th>Subtotal</th>
                <th></th>
              </tr>
            </thead>
            <tbody id="cart-body">
              <?php foreach ($cart_items as $item):
                $img = $item['image'] ?: 'https://via.placeholder.com/60x60?text=' . urlencode($item['name']);
                $subtotal = $item['price'] * $item['quantity'];
              ?>
              <tr data-pid="<?= $item['product_id'] ?>">
                <td>
                  <div class="d-flex align-items-center gap-2">
                    <img src="<?= htmlspecialchars($img) ?>" width="60" height="60" class="rounded object-fit-cover" alt="">
                    <a href="/product-detail.php?id=<?= $item['product_id'] ?>" class="text-decoration-none fw-semibold">
                      <?= htmlspecialchars($item['name']) ?>
                    </a>
                  </div>
                </td>
                <td>$<?= number_format($item['price'], 2) ?></td>
                <td style="width:140px">
                  <div class="input-group input-group-sm">
                    <button class="btn btn-outline-secondary btn-qty-minus" type="button">−</button>
                    <input type="number" class="form-control text-center item-qty" value="<?= $item['quantity'] ?>"
                           min="1" max="<?= min(10, $item['stock']) ?>" data-price="<?= $item['price'] ?>">
                    <button class="btn btn-outline-secondary btn-qty-plus" type="button">+</button>
                  </div>
                </td>
                <td class="subtotal fw-semibold">$<?= number_format($subtotal, 2) ?></td>
                <td>
                  <button class="btn btn-sm btn-outline-danger btn-remove" data-pid="<?= $item['product_id'] ?>">
                    <i class="fas fa-trash"></i>
                  </button>
                </td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
      <div class="mt-3 d-flex gap-2">
        <button id="btn-update-cart" class="btn btn-secondary"><i class="fas fa-sync me-1"></i>Update Cart</button>
        <a href="/products.php" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i>Continue Shopping</a>
      </div>
    </div>

    <!-- Order summary -->
    <div class="col-lg-4">
      <div class="card shadow-sm">
        <div class="card-header bg-primary text-white fw-bold">Order Summary</div>
        <div class="card-body">
          <div class="d-flex justify-content-between mb-2">
            <span>Subtotal</span>
            <span id="cart-total">$<?= number_format($total, 2) ?></span>
          </div>
          <div class="d-flex justify-content-between mb-2">
            <span>Shipping</span>
            <span class="text-success"><?= $total >= 50 ? 'Free' : '$5.99' ?></span>
          </div>
          <hr>
          <div class="d-flex justify-content-between fw-bold fs-5 mb-3">
            <span>Total</span>
            <span id="cart-grand-total">$<?= number_format($total >= 50 ? $total : $total + 5.99, 2) ?></span>
          </div>
          <div class="d-grid">
            <a href="/checkout.php" class="btn btn-primary btn-lg">
              <i class="fas fa-credit-card me-1"></i>Proceed to Checkout
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
  <?php endif; ?>
</div>

<div id="toast-container" class="position-fixed bottom-0 end-0 p-3" style="z-index:9999"></div>

<script>
$(function () {
    // Qty controls
    $(document).on('click', '.btn-qty-minus', function () {
        var input = $(this).siblings('.item-qty');
        var v = parseInt(input.val(), 10);
        if (v > 1) input.val(v - 1).trigger('change');
    });
    $(document).on('click', '.btn-qty-plus', function () {
        var input = $(this).siblings('.item-qty');
        var v = parseInt(input.val(), 10);
        var max = parseInt(input.attr('max'), 10);
        if (v < max) input.val(v + 1).trigger('change');
    });
    $(document).on('change', '.item-qty', function () {
        var row = $(this).closest('tr');
        var price = parseFloat($(this).data('price'));
        var qty = parseInt($(this).val(), 10);
        row.find('.subtotal').text('$' + (price * qty).toFixed(2));
        recalcTotal();
    });

    function recalcTotal() {
        var sub = 0;
        $('.item-qty').each(function () {
            sub += parseFloat($(this).data('price')) * parseInt($(this).val(), 10);
        });
        $('#cart-total').text('$' + sub.toFixed(2));
        var grand = sub >= 50 ? sub : sub + 5.99;
        $('#cart-grand-total').text('$' + grand.toFixed(2));
    }

    // Update cart
    $('#btn-update-cart').on('click', function () {
        var updates = [];
        $('tr[data-pid]').each(function () {
            updates.push({ product_id: $(this).data('pid'), quantity: parseInt($(this).find('.item-qty').val(), 10) });
        });
        var pending = updates.length;
        if (!pending) return;
        updates.forEach(function (u) {
            $.post('/ajax/cart.php', { action: 'update', product_id: u.product_id, quantity: u.quantity }, function () {
                pending--;
                if (pending === 0) { showToast('Cart updated!', 'success'); updateCartBadge(); }
            }, 'json');
        });
    });

    // Remove item
    $(document).on('click', '.btn-remove', function () {
        var btn = $(this);
        var pid = btn.data('pid');
        $.post('/ajax/cart.php', { action: 'remove', product_id: pid }, function (res) {
            if (res.success) {
                btn.closest('tr').remove();
                recalcTotal();
                updateCartBadge();
                showToast('Item removed.', 'info');
                if ($('tr[data-pid]').length === 0) location.reload();
            }
        }, 'json');
    });

    function showToast(msg, type) {
        var id = 'toast-' + Date.now();
        $('#toast-container').append(
            '<div id="' + id + '" class="toast align-items-center text-bg-' + type + ' border-0 show" role="alert">'
            + '<div class="d-flex"><div class="toast-body">' + msg + '</div>'
            + '<button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>'
            + '</div></div>'
        );
        setTimeout(function () { $('#' + id).remove(); }, 3000);
    }
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
