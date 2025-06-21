<?php
include 'admin_auth.php';
include 'config.php';

$orders = $conn->query('SELECT * FROM orders ORDER BY id DESC');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Customer Orders</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <style>
    body { background: #f4f6fa; }
    .admin-nav { background: #2a3b8f; }
    .admin-nav .nav-link, .admin-nav .navbar-brand { color: #fff !important; }
    .admin-nav .nav-link.active { font-weight: bold; text-decoration: underline; }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg admin-nav mb-4">
  <div class="container">
    <a class="navbar-brand fw-bold" href="admin_dashboard.php"><i class="bi bi-speedometer2 me-2"></i>Admin Panel</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#adminNavbar" aria-controls="adminNavbar" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="adminNavbar">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a class="nav-link" href="admin_products.php"><i class="bi bi-box-seam me-1"></i>Products</a></li>
        <li class="nav-item"><a class="nav-link active" href="admin_orders.php"><i class="bi bi-receipt me-1"></i>Orders</a></li>
        <li class="nav-item"><a class="nav-link" href="admin_logout.php"><i class="bi bi-box-arrow-right me-1"></i>Logout</a></li>
      </ul>
    </div>
  </div>
</nav>
<div class="container">
  <div class="card shadow-sm mb-4">
    <div class="card-body">
      <h2 class="mb-4">Customer Orders</h2>
      <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle bg-white">
          <thead class="table-light">
            <tr>
              <th>ID</th>
              <th>Name</th>
              <th>Email</th>
              <th>Phone</th>
              <th>Address</th>
              <th>Products</th>
              <th>Total</th>
              <th>Date</th>
            </tr>
          </thead>
          <tbody>
            <?php while ($o = $orders->fetch_assoc()): ?>
            <tr>
              <td><?= $o['id'] ?></td>
              <td><?= htmlspecialchars($o['name']) ?></td>
              <td><?= htmlspecialchars($o['email']) ?></td>
              <td><?= htmlspecialchars($o['phone']) ?></td>
              <td><?= htmlspecialchars($o['address']) ?></td>
              <td>
                <?php
                $items = $conn->query('SELECT oi.*, p.name FROM order_items oi JOIN products p ON oi.product_id=p.id WHERE oi.order_id=' . $o['id']);
                while ($item = $items->fetch_assoc()) {
                    echo '<span class="badge bg-secondary me-1">' . htmlspecialchars($item['name']) . ' x ' . $item['quantity'] . ' ($' . number_format($item['price'], 2) . ')</span><br>';
                }
                ?>
              </td>
              <td class="fw-bold text-success">$<?= number_format($o['total'], 2) ?></td>
              <td><?= $o['created_at'] ?></td>
            </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      </div>
      <a href="admin_dashboard.php" class="btn btn-link mt-2"><i class="bi bi-arrow-left"></i> Back to Dashboard</a>
    </div>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 