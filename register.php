<?php
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/auth.php';

if (is_logged_in()) {
    header('Location: /index.php');
    exit;
}

$errors  = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    $name     = trim($_POST['name']     ?? '');
    $email    = trim($_POST['email']    ?? '');
    $password = $_POST['password']         ?? '';
    $confirm  = $_POST['confirm_password'] ?? '';

    if (!$name)                                          $errors[] = 'Name is required.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL))      $errors[] = 'Valid email is required.';
    if (strlen($password) < 8)                           $errors[] = 'Password must be at least 8 characters.';
    if (!preg_match('/[A-Z]/', $password))               $errors[] = 'Password must contain at least one uppercase letter.';
    if (!preg_match('/[0-9]/', $password))               $errors[] = 'Password must contain at least one number.';
    if ($password !== $confirm)                          $errors[] = 'Passwords do not match.';

    if (empty($errors)) {
        // Check for duplicate email
        $chk = $pdo->prepare('SELECT id FROM users WHERE email = ?');
        $chk->execute([$email]);
        if ($chk->fetch()) {
            $errors[] = 'An account with that email already exists.';
        } else {
            $hash = password_hash($password, PASSWORD_BCRYPT);
            $ins  = $pdo->prepare('INSERT INTO users (name, email, password) VALUES (?,?,?)');
            $ins->execute([$name, $email, $hash]);
            header('Location: /login.php?registered=1');
            exit;
        }
    }
}

$page_title = 'Register';
require_once __DIR__ . '/includes/header.php';
?>

<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-md-6">
      <div class="card shadow">
        <div class="card-header bg-success text-white text-center py-3">
          <h4 class="mb-0"><i class="fas fa-user-plus me-1"></i>Create an Account</h4>
        </div>
        <div class="card-body p-4">
          <?php if ($errors): ?>
          <div class="alert alert-danger">
            <ul class="mb-0">
              <?php foreach ($errors as $e): ?>
              <li><?= htmlspecialchars($e) ?></li>
              <?php endforeach; ?>
            </ul>
          </div>
          <?php endif; ?>

          <form method="POST" action="/register.php">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token()) ?>">

            <div class="mb-3">
              <label class="form-label">Full Name</label>
              <div class="input-group">
                <span class="input-group-text"><i class="fas fa-user"></i></span>
                <input type="text" name="name" class="form-control" placeholder="John Doe"
                       value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" required>
              </div>
            </div>

            <div class="mb-3">
              <label class="form-label">Email Address</label>
              <div class="input-group">
                <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                <input type="email" name="email" class="form-control" placeholder="you@example.com"
                       value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
              </div>
            </div>

            <div class="mb-3">
              <label class="form-label">Password <small class="text-muted">(min 8 chars, 1 uppercase, 1 number)</small></label>
              <div class="input-group">
                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                <input type="password" name="password" class="form-control" placeholder="Create a strong password" required>
              </div>
            </div>

            <div class="mb-4">
              <label class="form-label">Confirm Password</label>
              <div class="input-group">
                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                <input type="password" name="confirm_password" class="form-control" placeholder="Repeat your password" required>
              </div>
            </div>

            <div class="d-grid">
              <button type="submit" class="btn btn-success btn-lg">
                <i class="fas fa-user-plus me-1"></i>Create Account
              </button>
            </div>
          </form>
        </div>
        <div class="card-footer text-center text-muted">
          Already have an account? <a href="/login.php" class="fw-semibold">Login here</a>
        </div>
      </div>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
