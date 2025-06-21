<?php
include 'admin_auth.php';
include 'config.php';

$categories = ['Laptops', 'RAM', 'Monitors', 'Accessories', 'Components'];
$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $desc = $_POST['description'] ?? '';
    $price = floatval($_POST['price'] ?? 0);
    $cat = $_POST['category'] ?? '';
    if ($name && $cat && $price > 0) {
        $stmt = $conn->prepare('INSERT INTO products (name, description, price, category) VALUES (?, ?, ?, ?)');
        $stmt->bind_param('ssds', $name, $desc, $price, $cat);
        $stmt->execute();
        $pid = $stmt->insert_id;
        $stmt->close();
        $msg = 'Product added!';
    } else {
        $msg = 'Please fill all fields correctly.';
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Add Product</title>
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
  <div class="card shadow-sm mx-auto" style="max-width:600px;">
    <div class="card-body">
      <h2 class="card-title mb-4">Add Product</h2>
      <?php if ($msg): ?><div class="alert alert-info py-2"> <?= htmlspecialchars($msg) ?> </div><?php endif; ?>
      <form method="post" enctype="multipart/form-data">
        <div class="mb-3">
          <label class="form-label">Name</label>
          <input type="text" name="name" class="form-control" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Description</label>
          <textarea name="description" class="form-control"></textarea>
        </div>
        <div class="mb-3">
          <label class="form-label">Price</label>
          <input type="number" name="price" step="0.01" class="form-control" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Category</label>
          <select name="category" class="form-select" required>
            <option value="">Select</option>
            <?php foreach ($categories as $c): ?>
              <option value="<?= $c ?>"><?= $c ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="mb-3">
          <label class="form-label">Photos</label>
          <input type="file" name="photos[]" multiple class="form-control">
        </div>
        <button type="submit" class="btn btn-success w-100"><i class="bi bi-plus-lg me-1"></i> Add Product</button>
      </form>
      <a href="admin_products.php" class="btn btn-link mt-3"><i class="bi bi-arrow-left"></i> Back to Products</a>
    </div>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 