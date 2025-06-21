<?php
include 'admin_auth.php';
include 'config.php';
$id = intval($_GET['id'] ?? 0);
if (!$id) die('Invalid product ID');
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $conn->query("DELETE FROM products WHERE id=$id");
    $conn->query("DELETE FROM product_images WHERE product_id=$id");
    header('Location: admin_products.php');
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Delete Product</title>
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
    <div class="collapse navbar-collapse">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a class="nav-link" href="admin_products.php"><i class="bi bi-box-seam me-1"></i>Products</a></li>
        <li class="nav-item"><a class="nav-link" href="admin_orders.php"><i class="bi bi-receipt me-1"></i>Orders</a></li>
        <li class="nav-item"><a class="nav-link" href="admin_logout.php"><i class="bi bi-box-arrow-right me-1"></i>Logout</a></li>
      </ul>
    </div>
  </div>
</nav>
<div class="container">
  <div class="card shadow-sm mx-auto" style="max-width:500px;">
    <div class="card-body text-center">
      <h2 class="card-title mb-3 text-danger"><i class="bi bi-exclamation-triangle me-2"></i>Delete Product</h2>
      <p class="mb-4">Are you sure you want to delete this product? This action cannot be undone.</p>
      <form method="post" class="d-inline">
        <button type="submit" class="btn btn-danger"><i class="bi bi-trash me-1"></i> Yes, Delete</button>
      </form>
      <a href="admin_products.php" class="btn btn-secondary ms-2"><i class="bi bi-arrow-left"></i> Cancel</a>
    </div>
  </div>
</div>
</body>
</html> 