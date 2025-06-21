<?php
session_start();
include 'config.php';
if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];
// Add to cart
if (isset($_POST['add'])) {
    $pid = intval($_POST['product_id']);
    $qty = max(1, intval($_POST['quantity']));
    if (isset($_SESSION['cart'][$pid])) {
        $_SESSION['cart'][$pid] += $qty;
    } else {
        $_SESSION['cart'][$pid] = $qty;
    }
    // If AJAX, return JSON
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        header('Content-Type: application/json');
        $count = array_sum($_SESSION['cart']);
        echo json_encode(['success' => true, 'count' => $count]);
        exit();
    }
    header('Location: cart.php');
    exit();
}
// Update cart
if (isset($_POST['update'])) {
    foreach ($_POST['qty'] as $pid => $qty) {
        if ($qty <= 0) unset($_SESSION['cart'][$pid]);
        else $_SESSION['cart'][$pid] = $qty;
    }
}
// Remove item
if (isset($_GET['remove'])) {
    unset($_SESSION['cart'][intval($_GET['remove'])]);
    header('Location: cart.php');
    exit();
}
// Fetch products
$cart = $_SESSION['cart'];
$ids = array_keys($cart);
$products = [];
$total = 0;
if ($ids) {
    $idstr = implode(',', array_map('intval', $ids));
    $res = $conn->query("SELECT * FROM products WHERE id IN ($idstr)");
    while ($p = $res->fetch_assoc()) {
        $p['qty'] = $cart[$p['id']];
        $p['subtotal'] = $p['qty'] * $p['price'];
        $products[] = $p;
        $total += $p['subtotal'];
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Cart</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="style.css">
</head>
<body style="background:#f4f6fa;">
<nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
  <div class="container">
    <a class="navbar-brand fw-bold text-black" href="index.php">PC Shop</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a class="nav-link text-black" href="index.php">Home</a></li>
        <li class="nav-item">
          <a class="nav-link position-relative text-black" href="cart.php">
            <i class="bi bi-cart3" style="font-size:1.3em;"></i>
            <span id="cartCounter" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-warning text-dark" style="font-size:0.9em;<?= (isset($_SESSION['cart']) && array_sum($_SESSION['cart'])) ? '' : 'display:none;' ?>">
              <?= isset($_SESSION['cart']) ? array_sum($_SESSION['cart']) : 0 ?>
            </span>
          </a>
        </li>
      </ul>
    </div>
  </div>
</nav>
<div class="container py-4">
    <div class="mb-3"><a href="index.php" class="btn btn-outline-primary"><i class="bi bi-arrow-left"></i> Continue Shopping</a></div>
    <div class="row justify-content-center">
        <div class="col-lg-9">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="card-title mb-4">Your Cart</h2>
                    <?php if ($products): ?>
                    <form method="post">
                    <div class="table-responsive">
                    <table class="table align-middle table-bordered table-hover bg-white">
                        <thead class="table-light">
                        <tr><th>Name</th><th>Price</th><th>Qty</th><th>Subtotal</th><th>Action</th></tr>
                        </thead>
                        <tbody>
                        <?php foreach ($products as $p): ?>
                        <tr>
                            <td><?= htmlspecialchars($p['name']) ?></td>
                            <td class="text-primary fw-semibold">$<?= number_format($p['price'],2) ?></td>
                            <td style="max-width:80px;"><input type="number" name="qty[<?= $p['id'] ?>]" value="<?= $p['qty'] ?>" min="1" class="form-control"></td>
                            <td class="fw-bold">$<?= number_format($p['subtotal'],2) ?></td>
                            <td><a href="cart.php?remove=<?= $p['id'] ?>" class="btn btn-sm btn-danger"><i class="bi bi-trash"></i> Remove</a></td>
                        </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <button type="submit" name="update" class="btn btn-primary"><i class="bi bi-arrow-repeat"></i> Update Cart</button>
                        <div class="fs-5 fw-bold">Total: <span class="text-success">$<?= number_format($total,2) ?></span></div>
                    </div>
                    </form>
                    <div class="text-end mt-4">
                        <a href="checkout.php" class="btn btn-success btn-lg"><i class="bi bi-credit-card"></i> Proceed to Checkout</a>
                    </div>
                    <?php else: ?>
                    <div class="alert alert-info">Your cart is empty.</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 