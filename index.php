<?php
session_start();
include 'config.php';
$cat = $_GET['category'] ?? '';
$search = trim($_GET['search'] ?? '');
$min_price = isset($_GET['min_price']) ? floatval($_GET['min_price']) : '';
$max_price = isset($_GET['max_price']) ? floatval($_GET['max_price']) : '';
$categories = ['Laptops', 'RAM', 'Monitors', 'Accessories', 'Components'];
$where = [];
if ($cat && in_array($cat, $categories)) $where[] = "category='$cat'";
if ($search) {
    $safe = $conn->real_escape_string($search);
    $where[] = "(name LIKE '%$safe%' OR description LIKE '%$safe%')";
}
if ($min_price !== '' && is_numeric($min_price)) $where[] = "price >= $min_price";
if ($max_price !== '' && is_numeric($max_price)) $where[] = "price <= $max_price";
$where_sql = $where ? 'WHERE ' . implode(' AND ', $where) : '';
$products = $conn->query("SELECT * FROM products $where_sql ORDER BY id DESC");
$cart_count = isset($_SESSION['cart']) ? array_sum($_SESSION['cart']) : 0;
?>
<!DOCTYPE html>
<html>
<head>
    <title>PC Shop</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="style.css">
    <style>
    .suggest-list {
      position: absolute;
      z-index: 9999;
      background: #fff;
      border: 1px solid #ddd;
      border-radius: 0 0 8px 8px;
      box-shadow: 0 4px 16px rgba(0,0,0,0.08);
      width: 100%;
      max-height: 260px;
      overflow-y: auto;
      margin-top: 2px;
      left: 0;
      top: 100%;
    }
    .suggest-item {
      padding: 8px 14px;
      cursor: pointer;
      transition: background 0.15s;
    }
    .suggest-item:hover, .suggest-item.active {
      background: #f4f6fa;
    }
    .search-form-rel {
      position: relative !important;
    }
    #filterPanel {
      display: none;
      position: fixed;
      top: 80px;
      right: 32px;
      z-index: 2100;
      background: #fff;
      border-radius: 12px;
      box-shadow: 0 8px 32px rgba(0,0,0,0.13);
      padding: 24px 28px 18px 28px;
      min-width: 320px;
      max-width: 95vw;
    }
    #filterPanel.active { display: block; }
    @media (max-width: 600px) {
      #filterPanel { right: 8px; left: 8px; min-width: unset; }
    }
    </style>
</head>
<body style="background:#f4f6fa;">
<nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
  <div class="container position-relative">
    <a class="navbar-brand fw-bold text-black" href="index.php">PC Shop</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <button id="filterIconBtn" class="btn btn-link nav-link text-black p-0 ms-2 position-absolute end-0 top-50 translate-middle-y d-none d-lg-inline" style="font-size:1.4em;z-index:1100;" type="button"><i class="bi bi-funnel"></i></button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto align-items-lg-center">
        <li class="nav-item"><a class="nav-link text-black" href="index.php">Home</a></li>
        <li class="nav-item">
          <a class="nav-link position-relative text-black" href="cart.php">
            <i class="bi bi-cart3" style="font-size:1.3em;"></i>
            <span id="cartCounter" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-warning text-dark" style="font-size:0.9em;<?= ($cart_count) ? '' : 'display:none;' ?>">
              <?= $cart_count ?>
            </span>
          </a>
        </li>
      </ul>
      <form class="d-flex ms-lg-3 mt-2 mt-lg-0 search-form-rel" method="get" action="index.php" role="search" style="max-width:320px;">
        <input class="form-control me-2" id="searchInput" autocomplete="off" type="search" name="search" placeholder="Search products..." value="<?= htmlspecialchars($search) ?>">
        <button class="btn btn-outline-dark" type="submit"><i class="bi bi-search"></i></button>
        <div id="suggestBox" class="suggest-list d-none"></div>
      </form>
      <form method="get" class="filter-price-form mt-3 mt-lg-0 ms-lg-3 p-3 rounded bg-light border d-lg-none" style="min-width:220px;max-width:340px;">
        <input type="hidden" name="search" value="<?= htmlspecialchars($search) ?>">
        <div class="mb-2">
          <label class="form-label">Category</label>
          <select name="category" class="form-select">
            <option value="">All Categories</option>
            <?php foreach ($categories as $c): ?>
              <option value="<?= $c ?>" <?= $cat == $c ? 'selected' : '' ?>><?= $c ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="mb-2 d-flex align-items-center gap-2">
          <label class="me-1 mb-0">Price:</label>
          <input type="number" name="min_price" step="0.01" min="0" placeholder="Min" value="<?= htmlspecialchars($min_price) ?>">
          <span>-</span>
          <input type="number" name="max_price" step="0.01" min="0" placeholder="Max" value="<?= htmlspecialchars($max_price) ?>">
        </div>
        <div class="d-flex gap-2 mt-3">
          <button class="btn btn-primary btn-sm px-4" type="submit">Apply</button>
          <?php if ($min_price !== '' || $max_price !== '' || $cat): ?>
            <a href="index.php?<?= http_build_query(array_filter(['search'=>$search])) ?>" class="btn btn-link btn-sm">Clear</a>
          <?php endif; ?>
        </div>
      </form>
    </div>
  </div>
</nav>
<div class="container">
    <div id="cartMsg" class="alert alert-success d-none" role="alert" style="position:fixed;top:80px;right:30px;z-index:9999;min-width:180px;"></div>
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="fw-bold">PC Shop</h2>
        <a href="cart.php" class="btn btn-outline-primary">View Cart</a>
    </div>
    <?php if ($search): ?>
      <div class="alert alert-info mb-4">Search results for <b><?= htmlspecialchars($search) ?></b>:</div>
    <?php endif; ?>
    <div class="row g-4">
    <?php if ($products->num_rows): ?>
      <?php while ($p = $products->fetch_assoc()): ?>
        <div class="col-md-4 col-sm-6">
            <div class="card h-100 shadow-sm product-card">
                <?php
                $img = $conn->query('SELECT * FROM product_images WHERE product_id=' . $p['id'] . ' LIMIT 1')->fetch_assoc();
                if ($img) echo '<a href="product.php?id=' . $p['id'] . '"><img src="' . $img['image_path'] . '" class="card-img-top" style="object-fit:cover;max-height:180px;"></a>';
                ?>
                <div class="card-body d-flex flex-column">
                    <a href="product.php?id=<?= $p['id'] ?>" class="text-decoration-none text-dark"><h5 class="card-title fw-semibold mb-1"><?= htmlspecialchars($p['name']) ?></h5></a>
                    <div class="mb-2"><span class="text-primary fw-bold fs-5">$<?= number_format($p['price'],2) ?></span></div>
                    <div class="mb-2"><span class="badge bg-secondary">Category: <?= htmlspecialchars($p['category']) ?></span></div>
                    <p class="card-text flex-grow-1" style="min-height:48px;"> <?= nl2br(htmlspecialchars($p['description'])) ?> </p>
                    <form class="addToCartForm d-flex align-items-center gap-2 mt-2" method="post" action="./cart.php">
                        <input type="hidden" name="product_id" value="<?= $p['id'] ?>">
                        <input type="number" name="quantity" value="1" min="1" class="form-control" style="width:70px;">
                        <button type="submit" name="add" class="btn btn-primary">Add to Cart</button>
                    </form>
                </div>
            </div>
        </div>
      <?php endwhile; ?>
    <?php else: ?>
      <div class="col-12"><div class="alert alert-warning">No products found matching your search.</div></div>
    <?php endif; ?>
    </div>
</div>
<div id="filterPanel">
  <form method="get" class="filter-price-form mb-2">
    <input type="hidden" name="search" value="<?= htmlspecialchars($search) ?>">
    <div class="mb-2">
      <label class="form-label">Category</label>
      <select name="category" class="form-select">
        <option value="">All Categories</option>
        <?php foreach ($categories as $c): ?>
          <option value="<?= $c ?>" <?= $cat == $c ? 'selected' : '' ?>><?= $c ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="mb-2 d-flex align-items-center gap-2">
      <label class="me-1 mb-0">Price:</label>
      <input type="number" name="min_price" step="0.01" min="0" placeholder="Min" value="<?= htmlspecialchars($min_price) ?>">
      <span>-</span>
      <input type="number" name="max_price" step="0.01" min="0" placeholder="Max" value="<?= htmlspecialchars($max_price) ?>">
    </div>
    <div class="d-flex gap-2 mt-3">
      <button class="btn btn-primary btn-sm px-4" type="submit">Apply</button>
      <?php if ($min_price !== '' || $max_price !== '' || $cat): ?>
        <a href="index.php?<?= http_build_query(array_filter(['search'=>$search])) ?>" class="btn btn-link btn-sm">Clear</a>
      <?php endif; ?>
    </div>
  </form>
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
const searchInput = document.getElementById('searchInput');
const suggestBox = document.getElementById('suggestBox');
let suggestItems = [];
let activeIndex = -1;
searchInput.addEventListener('input', function() {
  const val = this.value.trim();
  if (val.length < 1) { suggestBox.classList.add('d-none'); return; }
  fetch('search_suggest.php?q=' + encodeURIComponent(val))
    .then(res => res.json())
    .then(data => {
      suggestBox.innerHTML = '';
      if (!data.length) { suggestBox.classList.add('d-none'); return; }
      data.forEach((item, i) => {
        const div = document.createElement('div');
        div.className = 'suggest-item';
        div.textContent = item.name;
        div.onclick = () => {
          searchInput.value = item.name;
          suggestBox.classList.add('d-none');
          searchInput.form.submit();
        };
        suggestBox.appendChild(div);
      });
      suggestBox.classList.remove('d-none');
      suggestItems = Array.from(suggestBox.children);
      activeIndex = -1;
    });
});
document.addEventListener('click', e => {
  if (!suggestBox.contains(e.target) && e.target !== searchInput) {
    suggestBox.classList.add('d-none');
  }
});
searchInput.addEventListener('keydown', function(e) {
  if (!suggestItems.length || suggestBox.classList.contains('d-none')) return;
  if (e.key === 'ArrowDown') {
    e.preventDefault();
    activeIndex = (activeIndex + 1) % suggestItems.length;
    updateActive();
  } else if (e.key === 'ArrowUp') {
    e.preventDefault();
    activeIndex = (activeIndex - 1 + suggestItems.length) % suggestItems.length;
    updateActive();
  } else if (e.key === 'Enter') {
    if (activeIndex >= 0) {
      e.preventDefault();
      suggestItems[activeIndex].click();
    }
  }
});
function updateActive() {
  suggestItems.forEach((el, i) => el.classList.toggle('active', i === activeIndex));
  if (activeIndex >= 0) suggestItems[activeIndex].scrollIntoView({block:'nearest'});
}
const filterPanel = document.getElementById('filterPanel');
const filterIconBtn = document.getElementById('filterIconBtn');
filterIconBtn.addEventListener('click', function(e) {
  e.stopPropagation();
  filterPanel.classList.toggle('active');
});
document.addEventListener('click', function(e) {
  if (filterPanel.classList.contains('active') && !filterPanel.contains(e.target) && e.target !== filterIconBtn) {
    filterPanel.classList.remove('active');
  }
});
// On mobile, clicking the filter icon opens the hamburger menu and scrolls to the filter form
filterIconBtn.addEventListener('click', function() {
  if (window.innerWidth < 992) {
    const navCollapse = document.getElementById('navbarNav');
    if (navCollapse && !navCollapse.classList.contains('show')) {
      navCollapse.classList.add('show');
      setTimeout(() => {
        const filterForm = document.querySelector('.filter-price-form');
        if (filterForm) filterForm.scrollIntoView({behavior:'smooth'});
      }, 300);
    }
  }
});
</script>
</body>
</html> 