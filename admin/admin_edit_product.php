<?php
include 'admin_auth.php';
include 'config.php';

$categories = ['Laptops', 'RAM', 'Monitors', 'Accessories', 'Components'];
$id = intval($_GET['id'] ?? 0);
if (!$id) die('Invalid product ID');

// Fetch product
$p = $conn->query("SELECT * FROM products WHERE id=$id")->fetch_assoc();
if (!$p) die('Product not found');

$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $desc = $_POST['description'] ?? '';
    $price = floatval($_POST['price'] ?? 0);
    $cat = $_POST['category'] ?? '';
    if ($name && $cat && $price > 0) {
        $stmt = $conn->prepare('UPDATE products SET name=?, description=?, price=?, category=? WHERE id=?');
        $stmt->bind_param('ssdsi', $name, $desc, $price, $cat, $id);
        $stmt->execute();
        $stmt->close();
        // Handle new images
        if (!empty($_FILES['photos']['name'][0])) {
            $dir = 'uploads/' . $id;
            if (!is_dir($dir)) mkdir($dir, 0777, true);
            foreach ($_FILES['photos']['tmp_name'] as $i => $tmp) {
                $fname = basename($_FILES['photos']['name'][$i]);
                $target = "$dir/$fname";
                if (move_uploaded_file($tmp, $target)) {
                    $conn->query("INSERT INTO product_images (product_id, image_path) VALUES ($id, '$target')");
                }
            }
        }
        $msg = 'Product updated!';
        // Refresh product data
        $p = $conn->query("SELECT * FROM products WHERE id=$id")->fetch_assoc();
    } else {
        $msg = 'Please fill all required fields.';
    }
}
// Delete image
if (isset($_GET['delimg'])) {
    $imgid = intval($_GET['delimg']);
    $img = $conn->query("SELECT * FROM product_images WHERE id=$imgid AND product_id=$id")->fetch_assoc();
    if ($img) {
        unlink($img['image_path']);
        $conn->query("DELETE FROM product_images WHERE id=$imgid");
    }
    header("Location: admin_edit_product.php?id=$id");
    exit();
}
// Fetch images
$imgs = $conn->query("SELECT * FROM product_images WHERE product_id=$id");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Product</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <style>
    body { background: #f4f6fa; }
    .admin-nav { background: #2a3b8f; }
    .admin-nav .nav-link, .admin-nav .navbar-brand { color: #fff !important; }
    .admin-nav .nav-link.active { font-weight: bold; text-decoration: underline; }
    .img-thumb { border-radius: 8px; box-shadow: 0 2px 8px rgba(42,59,143,0.08); }
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
  <div class="card shadow-sm mx-auto" style="max-width:700px;">
    <div class="card-body">
      <h2 class="card-title mb-4">Edit Product</h2>
      <?php if ($msg): ?><div class="alert alert-info py-2"> <?= htmlspecialchars($msg) ?> </div><?php endif; ?>
      <form method="post" enctype="multipart/form-data">
        <div class="mb-3">
          <label class="form-label">Name</label>
          <input type="text" name="name" value="<?= htmlspecialchars($p['name']) ?>" class="form-control" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Description</label>
          <textarea name="description" class="form-control"><?= htmlspecialchars($p['description']) ?></textarea>
        </div>
        <div class="mb-3">
          <label class="form-label">Price</label>
          <input type="number" name="price" step="0.01" value="<?= $p['price'] ?>" class="form-control" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Category</label>
          <select name="category" class="form-select" required>
            <?php foreach ($categories as $c): ?>
              <option value="<?= $c ?>" <?= $p['category'] == $c ? 'selected' : '' ?>><?= $c ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="mb-3">
          <label class="form-label">Add Photos</label>
          <input type="file" name="photos[]" multiple class="form-control">
        </div>
        <button type="submit" class="btn btn-primary w-100"><i class="bi bi-save me-1"></i> Update Product</button>
      </form>
      <h4 class="mt-4">Existing Photos</h4>
      <div class="row g-3">
        <?php while ($img = $imgs->fetch_assoc()): ?>
          <div class="col-6 col-md-4 col-lg-3 text-center">
            <img src="<?= $img['image_path'] ?>" class="img-fluid img-thumb mb-2" style="max-width:100px;max-height:100px;object-fit:cover;">
            <br>
            <a href="admin_edit_product.php?id=<?= $id ?>&delimg=<?= $img['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this image?')"><i class="bi bi-trash"></i> Delete</a>
          </div>
        <?php endwhile; ?>
      </div>
      <a href="admin_products.php" class="btn btn-link mt-3"><i class="bi bi-arrow-left"></i> Back to Products</a>
    </div>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 