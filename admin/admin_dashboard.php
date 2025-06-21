<?php
include 'admin_auth.php';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
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
        <li class="nav-item"><a class="nav-link" href="admin_orders.php"><i class="bi bi-receipt me-1"></i>Orders</a></li>
        <li class="nav-item"><a class="nav-link" href="admin_logout.php"><i class="bi bi-box-arrow-right me-1"></i>Logout</a></li>
      </ul>
    </div>
  </div>
</nav>
<div class="container">
  <div class="row justify-content-center">
    <div class="col-12">
      <div class="card shadow-sm">
        <div class="card-body text-center py-5">
          <h2 class="mb-4">Welcome to the Admin Dashboard</h2>
          <div class="d-flex flex-row flex-wrap flex-md-nowrap justify-content-center gap-3">
            <a href="admin_products.php" class="btn btn-outline-primary py-3 px-4 flex-fill" style="min-width:180px;"><i class="bi bi-box-seam me-2"></i>Manage Products</a>
            <a href="admin_orders.php" class="btn btn-outline-success py-3 px-4 flex-fill" style="min-width:180px;"><i class="bi bi-receipt me-2"></i>View Orders</a>
            <a href="admin_logout.php" class="btn btn-outline-danger py-3 px-4 flex-fill" style="min-width:180px;"><i class="bi bi-box-arrow-right me-2"></i>Logout</a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 