<?php
session_start();
session_destroy();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Logged Out</title>
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
  </div>
</nav>
<div class="container">
  <div class="card shadow-sm mx-auto mt-5" style="max-width:400px;">
    <div class="card-body text-center">
      <h2 class="card-title mb-3"><i class="bi bi-box-arrow-right me-2"></i>Logged Out</h2>
      <p class="mb-4">You have been logged out.</p>
      <a href="admin_login.php" class="btn btn-primary"><i class="bi bi-box-arrow-in-right me-1"></i> Login Again</a>
    </div>
  </div>
</div>
</body>
</html> 