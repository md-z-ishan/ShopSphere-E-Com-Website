<footer class="py-4 mt-5 footer">
  <div class="container">
    <div class="row">
      <div class="col-md-4 mb-3">
        <h5 class="text-white"><i class="fas fa-store me-1"></i>ShopSphere</h5>
        <p class="small">Your one-stop shop for electronics, clothing, books, and more. Quality products delivered to your door.</p>
      </div>
      <div class="col-md-4 mb-3">
        <h6 class="text-white">Quick Links</h6>
        <ul class="list-unstyled small">
          <li><a href="/index.php" class="text-secondary text-decoration-none">Home</a></li>
          <li><a href="/products.php" class="text-secondary text-decoration-none">Products</a></li>
          <li><a href="/cart.php" class="text-secondary text-decoration-none">Cart</a></li>
          <li><a href="/login.php" class="text-secondary text-decoration-none">Login</a></li>
        </ul>
      </div>
      <div class="col-md-4 mb-3">
        <h6 class="text-white">Contact</h6>
        <p class="small">
          <i class="fas fa-envelope me-1"></i>support@shopsphere.com<br>
          <i class="fas fa-phone me-1"></i>+1 (555) 123-4567
        </p>
        <div>
          <a href="#" class="text-secondary me-2"><i class="fab fa-facebook fa-lg"></i></a>
          <a href="#" class="text-secondary me-2"><i class="fab fa-twitter fa-lg"></i></a>
          <a href="#" class="text-secondary me-2"><i class="fab fa-instagram fa-lg"></i></a>
        </div>
      </div>
    </div>
    <hr class="border-secondary">
    <p class="text-center small mb-0">&copy; <?= date('Y') ?> ShopSphere. All rights reserved.</p>
  </div>
</footer>
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Update cart badge via AJAX after add-to-cart events
function updateCartBadge() {
    $.getJSON('/ajax/cart.php', { action: 'count' }, function(res) {
        var count = res.count || 0;
        var badge = $('#cart-count-badge');
        if (count > 0) {
            badge.text(count).show();
        } else {
            badge.hide();
        }
    });
}
</script>
</body>
</html>
