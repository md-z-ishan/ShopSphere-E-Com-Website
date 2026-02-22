<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth.php';

header('Content-Type: application/json');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$action     = $_POST['action'] ?? $_GET['action'] ?? '';
$product_id = (int)($_POST['product_id'] ?? $_GET['product_id'] ?? 0);
$quantity   = max(1, (int)($_POST['quantity'] ?? 1));
$logged_in  = is_logged_in();
$user_id    = $logged_in ? (int)$_SESSION['user_id'] : 0;

function json_response(array $data): void {
    echo json_encode($data);
    exit;
}

switch ($action) {
    // ------------------------------------------------------------------
    case 'add':
        if ($product_id <= 0) {
            json_response(['success' => false, 'message' => 'Invalid product.']);
        }

        // Validate product exists and has stock
        $stmt = $pdo->prepare('SELECT id, stock FROM products WHERE id = ?');
        $stmt->execute([$product_id]);
        $product = $stmt->fetch();

        if (!$product) {
            json_response(['success' => false, 'message' => 'Product not found.']);
        }
        if ($product['stock'] <= 0) {
            json_response(['success' => false, 'message' => 'Out of stock.']);
        }

        if ($logged_in) {
            // DB cart
            $ins = $pdo->prepare(
                'INSERT INTO cart (user_id, product_id, quantity) VALUES (?,?,?)
                 ON DUPLICATE KEY UPDATE quantity = quantity + VALUES(quantity)'
            );
            $ins->execute([$user_id, $product_id, $quantity]);
        } else {
            // Session cart
            if (!isset($_SESSION['cart'][$product_id])) {
                $_SESSION['cart'][$product_id] = ['quantity' => 0];
            }
            $_SESSION['cart'][$product_id]['quantity'] += $quantity;
        }

        json_response(['success' => true, 'message' => 'Item added to cart.', 'count' => cart_count($pdo, $logged_in, $user_id)]);
        break;

    // ------------------------------------------------------------------
    case 'remove':
        if ($product_id <= 0) {
            json_response(['success' => false, 'message' => 'Invalid product.']);
        }

        if ($logged_in) {
            $pdo->prepare('DELETE FROM cart WHERE user_id = ? AND product_id = ?')->execute([$user_id, $product_id]);
        } else {
            unset($_SESSION['cart'][$product_id]);
        }

        json_response(['success' => true, 'count' => cart_count($pdo, $logged_in, $user_id)]);
        break;

    // ------------------------------------------------------------------
    case 'update':
        if ($product_id <= 0) {
            json_response(['success' => false, 'message' => 'Invalid product.']);
        }
        if ($quantity < 1) $quantity = 1;

        if ($logged_in) {
            $pdo->prepare('UPDATE cart SET quantity = ? WHERE user_id = ? AND product_id = ?')
                ->execute([$quantity, $user_id, $product_id]);
        } else {
            if (isset($_SESSION['cart'][$product_id])) {
                $_SESSION['cart'][$product_id]['quantity'] = $quantity;
            }
        }

        json_response(['success' => true, 'count' => cart_count($pdo, $logged_in, $user_id)]);
        break;

    // ------------------------------------------------------------------
    case 'count':
        json_response(['success' => true, 'count' => cart_count($pdo, $logged_in, $user_id)]);
        break;

    // ------------------------------------------------------------------
    default:
        json_response(['success' => false, 'message' => 'Unknown action.']);
}

function cart_count(PDO $pdo, bool $logged_in, int $user_id): int {
    if ($logged_in) {
        $s = $pdo->prepare('SELECT COALESCE(SUM(quantity), 0) FROM cart WHERE user_id = ?');
        $s->execute([$user_id]);
        return (int)$s->fetchColumn();
    }
    $count = 0;
    foreach ($_SESSION['cart'] ?? [] as $item) {
        $count += (int)$item['quantity'];
    }
    return $count;
}
