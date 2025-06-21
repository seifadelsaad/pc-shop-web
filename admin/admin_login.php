<?php
session_start();
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header('Location: admin_dashboard.php');
    exit();
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    if ($email === 'admin@gmail.com' && $password === 'admin123') {
        $_SESSION['admin_logged_in'] = true;
        header('Location: admin_dashboard.php');
        exit();
    } else {
        $error = 'Invalid credentials';
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Login</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <style>
    body { background: #f4f6fa; }
    .login-card { max-width: 400px; margin: 7vh auto; }
    </style>
</head>
<body>
<div class="container">
  <div class="card login-card shadow-sm">
    <div class="card-body">
      <h3 class="card-title text-center mb-4">Admin Login</h3>
      <?php if ($error): ?><div class="alert alert-danger py-2"> <?= $error ?> </div><?php endif; ?>
      <form method="post">
        <div class="mb-3">
          <label class="form-label">Email</label>
          <input type="email" name="email" class="form-control" required autofocus>
        </div>
        <div class="mb-3">
          <label class="form-label">Password</label>
          <input type="password" name="password" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary w-100"><i class="bi bi-box-arrow-in-right me-1"></i> Login</button>
      </form>
    </div>
  </div>
</div>
</body>
</html> 