<?php
session_start();
include 'config.php';
$cart = $_SESSION['cart'] ?? [];
if (!$cart) {
    header('Location: cart.php');
    exit();
}
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
$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $address = $_POST['address'] ?? '';
    if ($name && $email && $phone && $address) {
        $stmt = $conn->prepare('INSERT INTO orders (name, email, phone, address, total, created_at) VALUES (?, ?, ?, ?, ?, NOW())');
        $stmt->bind_param('ssssd', $name, $email, $phone, $address, $total);
        $stmt->execute();
        $oid = $stmt->insert_id;
        $stmt->close();
        foreach ($products as $p) {
            $conn->query("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES ($oid, {$p['id']}, {$p['qty']}, {$p['price']})");
        }
        $_SESSION['cart'] = [];
        $msg = 'Order placed successfully!';
    } else {
        $msg = 'Please fill all fields.';
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Checkout</title>
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
    <div class="mb-3"><a href="cart.php" class="btn btn-outline-primary"><i class="bi bi-arrow-left"></i> Back to Cart</a></div>
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h2 class="card-title mb-4">Checkout</h2>
                    <?php if ($msg): ?>
                        <div class="alert alert-<?= $msg === 'Order placed successfully!' ? 'success' : 'danger' ?>"> <?= htmlspecialchars($msg) ?> </div>
                    <?php endif; ?>
                    <?php if (!$msg): ?>
                    <form method="post" class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Name</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Phone</label>
                            <input type="text" name="phone" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Address</label>
                            <textarea name="address" class="form-control" required></textarea>
                        </div>
                        <div class="col-12 text-end">
                            <button type="submit" class="btn btn-success btn-lg"><i class="bi bi-bag-check"></i> Place Order</button>
                        </div>
                    </form>
                    <?php endif; ?>
                </div>
            </div>
            <div class="card shadow-sm">
                <div class="card-body">
                    <h4 class="card-title mb-3">Order Summary</h4>
                    <ul class="list-group mb-3">
                        <?php foreach ($products as $i => $p): ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span>
                                <?= htmlspecialchars($p['name']) ?>
                                <span class="badge bg-secondary ms-2">x <?= $p['qty'] ?></span>
                                <button type="button" class="btn btn-link btn-sm p-0 ms-2" data-bs-toggle="modal" data-bs-target="#prodModal<?= $i ?>">Details</button>
                            </span>
                            <span class="fw-semibold">$<?= number_format($p['subtotal'],2) ?></span>
                        </li>
                        <!-- Modal for product details -->
                        <div class="modal fade" id="prodModal<?= $i ?>" tabindex="-1" aria-labelledby="prodModalLabel<?= $i ?>" aria-hidden="true">
                          <div class="modal-dialog">
                            <div class="modal-content">
                              <div class="modal-header">
                                <h5 class="modal-title" id="prodModalLabel<?= $i ?>">Product Details</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                              </div>
                              <div class="modal-body">
                                <h6><?= htmlspecialchars($p['name']) ?></h6>
                                <div class="mb-2"><b>Price:</b> $<?= number_format($p['price'],2) ?></div>
                                <div class="mb-2"><b>Quantity:</b> <?= $p['qty'] ?></div>
                                <div class="mb-2"><b>Description:</b><br> <?= nl2br(htmlspecialchars($p['description'])) ?></div>
                              </div>
                            </div>
                          </div>
                        </div>
                        <?php endforeach; ?>
                    </ul>
                    <div class="d-flex justify-content-between align-items-center fs-5 fw-bold">
                        <span>Total:</span>
                        <span class="text-success">$<?= number_format($total,2) ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 