<?php
session_start();
include 'config.php';
$id = intval($_GET['id'] ?? 0);
if (!$id) die('Product not found.');
$p = $conn->query("SELECT * FROM products WHERE id=$id")->fetch_assoc();
if (!$p) die('Product not found.');
$imgs = $conn->query("SELECT * FROM product_images WHERE product_id=$id");
$imgArr = [];
while ($img = $imgs->fetch_assoc()) $imgArr[] = $img['image_path'];
// Fetch related products
$cat = $conn->real_escape_string($p['category']);
$related = $conn->query("SELECT * FROM products WHERE category='$cat' AND id!=$id ORDER BY RAND() LIMIT 4");
$cart_count = isset($_SESSION['cart']) ? array_sum($_SESSION['cart']) : 0;
?>
<!DOCTYPE html>
<html>
<head>
    <title><?= htmlspecialchars($p['name']) ?> - Product Details</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="style.css">
    <style>
    .main-slider {
        display: flex;
        flex-direction: column;
        align-items: center;
        margin: 20px 0 10px 0;
    }
    .slider-img-wrap {
        position: relative;
        width: 340px;
        height: 260px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .slider-main-img {
        width: 340px;
        height: 260px;
        object-fit: contain;
        border-radius: 10px;
        box-shadow: 0 2px 12px rgba(42,59,143,0.10);
        background: #fff;
    }
    .slider-arrow-btn {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        background: #fff;
        color: #2a3b8f;
        border: 1px solid #c3c8d6;
        border-radius: 50%;
        width: 36px;
        height: 36px;
        font-size: 1.5rem;
        cursor: pointer;
        box-shadow: 0 2px 8px rgba(42,59,143,0.08);
        transition: background 0.2s, color 0.2s;
        z-index: 2;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .slider-arrow-btn:hover {
        background: #2a3b8f;
        color: #fff;
    }
    .slider-arrow-left { left: 8px; }
    .slider-arrow-right { right: 8px; }
    .slider-thumbs {
        display: flex;
        gap: 8px;
        margin-top: 4px;
    }
    .slider-thumb-img {
        width: 54px;
        height: 44px;
        object-fit: cover;
        border-radius: 6px;
        border: 2px solid #eee;
        cursor: pointer;
        transition: border 0.2s;
    }
    .slider-thumb-img.active {
        border: 2px solid #2a3b8f;
    }
    @media (max-width: 500px) {
        .slider-main-img, .slider-img-wrap { width: 98vw; max-width: 98vw; }
        .slider-main-img { height: 38vw; max-height: 220px; }
    }
    .related-title {
        margin-top: 40px;
        color: #2a3b8f;
        font-size: 1.2em;
        font-weight: 600;
    }
    .related-list {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 18px;
        margin-top: 16px;
    }
    .related-card {
        background: #f9fafd;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.04);
        padding: 14px;
        text-align: center;
        transition: box-shadow 0.2s;
    }
    .related-card:hover {
        box-shadow: 0 6px 24px rgba(42,59,143,0.10);
    }
    .related-card img {
        border-radius: 6px;
        max-width: 100%;
        max-height: 100px;
        margin-bottom: 8px;
    }
    .related-card b {
        color: #2a3b8f;
    }
    </style>
</head>
<body>
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
            <span id="cartCounter" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-warning text-dark" style="font-size:0.9em;<?= $cart_count ? '' : 'display:none;' ?>">
              <?= $cart_count ?>
            </span>
          </a>
        </li>
      </ul>
    </div>
  </div>
</nav>
<div class="container">
    <div id="cartMsg" class="alert alert-success d-none" role="alert" style="position:fixed;top:80px;right:30px;z-index:9999;min-width:180px;"></div>
    <a href="index.php">&lt; Back to Shop</a>
    <h2><?= htmlspecialchars($p['name']) ?></h2>
    <b>Category:</b> <?= htmlspecialchars($p['category']) ?><br>
    <b>Price:</b> $<?= number_format($p['price'],2) ?><br>
    <div class="main-slider">
        <div class="slider-img-wrap">
            <button type="button" class="slider-arrow-btn slider-arrow-left" onclick="prevImg()">&lt;</button>
            <img id="mainImg" class="slider-main-img" src="<?= count($imgArr) ? $imgArr[0] : '' ?>" alt="Product Image">
            <button type="button" class="slider-arrow-btn slider-arrow-right" onclick="nextImg()">&gt;</button>
        </div>
        <div class="slider-thumbs">
            <?php foreach ($imgArr as $i => $img): ?>
                <img src="<?= $img ?>" class="slider-thumb-img<?= $i==0 ? ' active' : '' ?>" onclick="showImg(<?= $i ?>)" id="thumb<?= $i ?>">
            <?php endforeach; ?>
        </div>
    </div>
    <p><?= nl2br(htmlspecialchars($p['description'])) ?></p>
    <form class="addToCartForm" method="post" action="./cart.php">
        <input type="hidden" name="product_id" value="<?= $p['id'] ?>">
        <input type="number" name="quantity" value="1" min="1" style="width:40px;">
        <button type="submit" name="add" class="btn btn-primary">Add to Cart</button>
    </form>
    <?php if ($related->num_rows): ?>
        <div class="related-title">Things you might want</div>
        <div class="related-list">
            <?php while ($r = $related->fetch_assoc()): ?>
                <div class="related-card">
                    <a href="product.php?id=<?= $r['id'] ?>">
                        <b><?= htmlspecialchars($r['name']) ?></b>
                    </a><br>
                    <span style="color:#4e6cf2;font-weight:600;">$<?= number_format($r['price'],2) ?></span><br>
                    <?php
                    $img = $conn->query('SELECT * FROM product_images WHERE product_id=' . $r['id'] . ' LIMIT 1')->fetch_assoc();
                    if ($img) echo '<img src="' . $img['image_path'] . '">';
                    ?>
                </div>
            <?php endwhile; ?>
        </div>
    <?php endif; ?>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.querySelectorAll('.addToCartForm').forEach(form => {
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        const fd = new FormData(form);
        fd.append('add', '');
        fetch('./cart.php', {
            method: 'POST',
            body: fd,
            headers: {'X-Requested-With': 'XMLHttpRequest'}
        })
        .then(async res => {
            const text = await res.text();
            let data;
            try {
                data = JSON.parse(text);
            } catch (err) {
                throw new Error(text);
            }
            return data;
        })
        .then(data => {
            if (data.success) {
                // Show message
                const msg = document.getElementById('cartMsg');
                msg.textContent = 'Added to cart!';
                msg.classList.remove('d-none','alert-danger');
                msg.classList.add('alert-success');
                setTimeout(() => msg.classList.add('d-none'), 1800);
                // Update cart counter
                const counter = document.getElementById('cartCounter');
                counter.textContent = data.count;
                counter.style.display = data.count > 0 ? '' : 'none';
            } else {
                throw new Error('Add to cart failed');
            }
        })
        .catch(err => {
            const msg = document.getElementById('cartMsg');
            msg.textContent = 'Error: ' + err.message;
            msg.classList.remove('d-none','alert-success');
            msg.classList.add('alert-danger');
            setTimeout(() => msg.classList.add('d-none'), 5000);
        });
    });
});
</script>
<script>
const images = <?= json_encode($imgArr) ?>;
let current = 0;
function showImg(idx) {
    current = idx;
    document.getElementById('mainImg').src = images[current];
    for (let i = 0; i < images.length; i++) {
        document.getElementById('thumb'+i).classList.remove('active');
    }
    document.getElementById('thumb'+current).classList.add('active');
}
function prevImg() {
    current = (current - 1 + images.length) % images.length;
    showImg(current);
}
function nextImg() {
    current = (current + 1) % images.length;
    showImg(current);
}
</script>
</body>
</html> 