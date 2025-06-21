<?php
include 'admin_auth.php';
include 'config.php';

// Fetch products
$result = $conn->query('SELECT * FROM products ORDER BY id DESC');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Manage Products</title>
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
        <li class="nav-item"><a class="nav-link active" href="admin_products.php"><i class="bi bi-box-seam me-1"></i>Products</a></li>
        <li class="nav-item"><a class="nav-link" href="admin_orders.php"><i class="bi bi-receipt me-1"></i>Orders</a></li>
        <li class="nav-item"><a class="nav-link" href="admin_logout.php"><i class="bi bi-box-arrow-right me-1"></i>Logout</a></li>
      </ul>
    </div>
  </div>
</nav>
<div class="container">
  <div class="card shadow-sm mb-4">
    <div class="card-body">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="mb-0">Manage Products</h2>
        <a href="admin_add_product.php" class="btn btn-success"><i class="bi bi-plus-lg me-1"></i> Add Product</a>
      </div>
      <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle bg-white">
          <thead class="table-light">
            <tr>
              <th>ID</th>
              <th>Name</th>
              <th>Category</th>
              <th>Price</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
              <td><?= $row['id'] ?></td>
              <td><?= htmlspecialchars($row['name']) ?></td>
              <td><?= htmlspecialchars($row['category']) ?></td>
              <td>$<?= number_format($row['price'], 2) ?></td>
              <td>
                <a href="admin_edit_product.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-primary"><i class="bi bi-pencil"></i> Edit</a>
                <a href="admin_delete_product.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-danger ms-1" onclick="return confirm('Delete this product?')"><i class="bi bi-trash"></i> Delete</a>
              </td>
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