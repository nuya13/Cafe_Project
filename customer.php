<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['customer_id'])) {
    header("Location: customer_login.php");
    exit();
}

require_once 'config/db.php';

$categories = $conn->query("SELECT * FROM categories")->fetchAll();

$sql = "SELECT p.*, c.name AS category_name 
        FROM products p 
        LEFT JOIN categories c ON p.category_id = c.id 
        ORDER BY p.id DESC";
$products = $conn->query($sql)->fetchAll();

$cart_count = isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0;
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Menu - Tomodachi Café</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .product-img-container {
            width: 100%;
            height: 160px;
            overflow: hidden;
            background-color: #F8F1EB;
            border-top-left-radius: 20px;
            border-top-right-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .product-img-container img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
        }
        .product-card:hover .product-img-container img {
            transform: scale(1.05);
        }
    </style>
</head>
<body>

<div class="container-fluid">
    <div class="row">
        
        <aside class="col-md-3 col-lg-2 sidebar p-4 d-flex flex-column justify-content-between">
            <div>
                <div class="brand-title mb-4 d-flex align-items-center gap-2">
                    <span>☕</span> Tomodachi Café
                </div>

                <div class="d-flex flex-column gap-2">
                    <a href="customer.php" class="nav-link-custom active">
                        <i class="bi bi-book"></i> Menu
                    </a>
                </div>
            </div>

            <div class="pt-3 border-top" style="border-color: var(--cafe-border) !important;">
                <a href="auth/login.php" class="nav-link-custom text-muted small px-0">
                    <i class="bi bi-shield-lock"></i> Staff Login
                </a>
            </div>
        </aside>

        <main class="col-md-9 col-lg-10 p-4 p-md-5">

            <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
                <div class="flex-grow-1" style="max-width: 450px;">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-0 rounded-start-pill ps-3 text-muted">
                            <i class="bi bi-search"></i>
                        </span>
                        <input type="text" id="searchInput" class="form-control border-0 rounded-end-pill py-2" placeholder="Search menu...">
                    </div>
                </div>

                <div class="d-flex align-items-center gap-2">
                    <a href="checkout.php" class="badge bg-white text-dark border p-2 px-3 rounded-pill shadow-sm d-flex align-items-center gap-2 text-decoration-none">
                        <i class="bi bi-cart3 fs-6" style="color: var(--cafe-primary) !important;"></i>
                        <span>Cart: <b id="cartCount" class="text-danger"><?= $cart_count; ?></b></span>
                    </a>

                    <?php if (!empty($_SESSION['customer_name'])): ?>
                        <div class="dropdown">
                            <button class="btn btn-white bg-white border rounded-pill px-3 py-2 small shadow-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                👋 สวัสดี, <?= htmlspecialchars($_SESSION['customer_name']); ?>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 rounded-3">
                                <li><a class="dropdown-item text-danger small" href="customer_logout.php"><i class="bi bi-box-arrow-right me-2"></i>ออกจากระบบ</a></li>
                            </ul>
                        </div>
                    <?php else: ?>
                        <a href="customer_login.php" class="btn btn-outline-secondary rounded-pill px-3 py-2 small" style="font-weight: 500;">
                            <i class="bi bi-person me-1"></i> เข้าสู่ระบบสมาชิก
                        </a>
                    <?php endif; ?>
                </div>
            </div>

            <h2 class="fw-normal mb-1">Coffee menu</h2>
            <p class="text-muted small mb-4">Select your favorite coffee, drinks, and bakery items.</p>

            <div class="d-flex flex-wrap gap-2 mb-4" id="categoryPills">
                <button type="button" class="category-pill active" data-category="">All</button>
                <?php foreach ($categories as $cat): ?>
                    <button type="button" class="category-pill" data-category="<?= htmlspecialchars($cat['name']); ?>">
                        <?= htmlspecialchars($cat['name']); ?>
                    </button>
                <?php endforeach; ?>
            </div>

            <div class="row g-4" id="productGrid">
                <?php if (count($products) > 0): ?>
                    <?php foreach ($products as $p): ?>
                        <div class="col-sm-6 col-md-6 col-lg-4 col-xl-3 product-item" 
                             data-name="<?= htmlspecialchars($p['name']); ?>" 
                             data-category="<?= htmlspecialchars($p['category_name'] ?? ''); ?>">
                            
                            <div class="card product-card shadow-sm h-100 d-flex flex-column justify-content-between border-0">
                                
                                <div class="product-img-container position-relative">
    <?php if (!empty($p['image'])): ?>
        <?php $img_src = (strpos($p['image'], 'http://') === 0 || strpos($p['image'], 'https://') === 0) ? $p['image'] : 'uploads/' . $p['image']; ?>
        <!-- รูปหลัก -->
        <img src="<?= htmlspecialchars($img_src); ?>" alt="<?= htmlspecialchars($p['name']); ?>" 
             onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
        
        <div class="text-muted fs-1 position-absolute top-50 start-50 translate-middle" style="display: none;">☕</div>
    <?php else: ?>
        <div class="text-muted fs-1">☕</div>
    <?php endif; ?>
</div>

                                <div class="p-4 d-flex flex-column justify-content-between flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h5 class="fw-normal mb-0 text-truncate me-2" style="color: var(--cafe-text-dark);"><?= htmlspecialchars($p['name']); ?></h5>
                                        <span class="product-price">฿<?= number_format($p['price'], 2); ?></span>
                                    </div>

                                    <div class="mt-auto pt-2">
                                        <?php if ($p['stock'] > 0): ?>
                                            <button onclick="addToSessionCart(<?= $p['id']; ?>, '<?= htmlspecialchars($p['name']); ?>', <?= $p['price']; ?>)" 
                                                    class="btn w-100 text-white rounded-pill py-2 shadow-sm" 
                                                    style="background-color: var(--cafe-primary); border: none; font-weight: 500;">
                                                <i class="bi bi-cart-plus me-1"></i> Add to Cart
                                            </button>
                                        <?php else: ?>
                                            <button class="btn w-100 btn-secondary rounded-pill py-2" disabled style="background-color: #a0938d; border: none; opacity: 0.85; font-weight: 400;">
                                                Sold Out
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </div>

                            </div>

                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-12 text-center py-5 text-muted">Do not have any products available at the moment.</div>
                <?php endif; ?>
            </div>

        </main>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
function addToSessionCart(productId, productName, productPrice) {
    let formData = new FormData();
    formData.append('product_id', productId);
    formData.append('product_name', productName);
    formData.append('price', productPrice);

    fetch('add_to_cart.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            document.getElementById('cartCount').innerText = data.total_items;
            Swal.fire({
                icon: 'success',
                title: 'เพิ่มสินค้าลงตะกร้าแล้ว',
                showConfirmButton: false,
                timer: 1000
            });
        }
    })
    .catch(error => console.error('Error:', error));
}
</script>

<script src="assets/js/customer.js"></script>
</body>
</html>