<?php
require_once 'config/db.php';

// ดึงหมวดหมู่
$categories =$conn->query("SELECT * FROM categories")->fetchAll();

// ดึงเมนูสินค้าทั้งหมด
$sql = "SELECT p.*, c.name AS category_name 
        FROM products p 
        LEFT JOIN categories c ON p.category_id = c.id 
        ORDER BY p.id DESC";
$products = $conn->query($sql)->fetchAll();
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tomodachi Cafe - Digital Menu</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar สำหรับลูกค้า -->
        <aside class="col-md-3 col-lg-2 sidebar p-4 d-flex flex-column justify-content-between">
            <div>
                <div class="brand-title mb-4 d-flex align-items-center gap-2">
                    <span>☕</span> Tomodachi Cafe
                </div>

                <div class="d-flex flex-column gap-2">
                    <a href="customer.php" class="nav-link-custom active">
                        <i class="bi bi-book"></i> Menu
                    </a>
                </div>
            </div>

            <!-- ลิงก์สำหรับพนักงาน/ผู้จัดการเข้าระบบหลังบ้าน -->
            <div class="pt-3 border-top" style="border-color: var(--cafe-border) !important;">
                <a href="auth/login.php" class="nav-link-custom text-muted small px-0">
                    <i class="bi bi-shield-lock"></i> Staff Login
                </a>
            </div>
        </aside>

        <!-- โซนสั่งอาหารของลูกค้า -->
        <main class="col-md-9 col-lg-10 p-4 p-md-5">

            <!-- แถบค้นหาด้านบน -->
            <div class="d-flex justify-content-between align-items-center mb-4 gap-3">
                <div class="flex-grow-1" style="max-width: 450px;">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-0 rounded-start-pill ps-3 text-muted">
                            <i class="bi bi-search"></i>
                        </span>
                        <input type="text" id="searchInput" class="form-control border-0 rounded-end-pill py-2" placeholder="Search menu...">
                    </div>
                </div>

                <!-- แสดงจำนวนชิ้นในตะกร้าจำลอง -->
                <div class="badge bg-white text-dark border p-2 px-3 rounded-pill shadow-sm d-flex align-items-center gap-2">
                    <i class="bi bi-cart3 text-warning fs-6" style="color: var(--cafe-primary) !important;"></i>
                    <span>Your Cart: <b id="cartCount" class="text-danger">0</b> Menu Items</span>
                </div>
            </div>

            <!-- หัวข้อและปุ่มหมวดหมู่ -->
            <h2 class="fw-bold mb-3">Coffee menu</h2>

            <div class="d-flex flex-wrap gap-2 mb-4" id="categoryPills">
                <button type="button" class="category-pill active" data-category="">All</button>
                <?php foreach ($categories as$cat): ?>
                    <button type="button" class="category-pill" data-category="<?= htmlspecialchars($cat['name']); ?>">
                        <?= htmlspecialchars($cat['name']); ?>
                    </button>
                <?php endforeach; ?>
            </div>

            <!-- การ์ดสินค้า (ไม่มีปุ่มแก้ไข/ลบ แต่มีปุ่มจำลองสั่งซื้อ) -->
            <div class="row g-4" id="productGrid">
                <?php if (count($products) > 0): ?>
                    <?php foreach ($products as$p): ?>
                        <div class="col-sm-6 col-md-6 col-lg-4 col-xl-3 product-item" 
                             data-name="<?= strtolower(htmlspecialchars($p['name'])); ?>" 
                             data-category="<?= strtolower(htmlspecialchars($p['category_name'] ?? '')); ?>">
                            
                            <div class="card product-card shadow-sm p-3">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h5 class="fw-bold mb-0 text-truncate me-2"><?= htmlspecialchars($p['name']); ?></h5>
                                    <span class="product-price">฿<?= number_format($p['price'], 2); ?></span>
                                </div>

                                <div class="text-muted small mb-3">
                                    Category: <span class="badge bg-light text-dark border"><?= htmlspecialchars($p['category_name'] ?? 'ทั่วไป'); ?></span>
                                </div>

                                <div class="d-flex justify-content-between align-items-center mt-auto pt-2 border-top">
                                    <small class="text-muted">
                                        Stock: <b><?= $p['stock']; ?></b>
                                    </small>
                                    
                                    <?php if ($p['stock'] > 0): ?>
                                        <button onclick="addToCart('<?= htmlspecialchars($p['name']); ?>', <?=$p['price']; ?>)" 
                                                class="btn btn-sm text-white px-3 rounded-pill fw-bold" 
                                                style="background-color: var(--cafe-primary); border: none;">
                                            <i class="bi bi-cart-plus me-1"></i> Add to Cart
                                        </button>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Sold Out</span>
                                    <?php endif; ?>
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
// Filter & Search ทำงานร่วมกันแบบ Real-time
const searchInput = document.getElementById('searchInput');
const categoryPills = document.querySelectorAll('.category-pill');
const productItems = document.querySelectorAll('.product-item');
let activeCategory = '';

function runFilter() {
    const searchTerm = searchInput.value.toLowerCase().trim();

    productItems.forEach(item => {
        const name = (item.getAttribute('data-name') || '').toLowerCase().trim();
        const category = (item.getAttribute('data-category') || '').toLowerCase().trim();

        const matchName = name.includes(searchTerm);
        const matchCategory = (activeCategory === '') || (category === activeCategory);

        if (matchName && matchCategory) {
            item.style.display = '';
        } else {
            item.style.display = 'none';
        }
    });
}

searchInput.addEventListener('input', runFilter);

categoryPills.forEach(pill => {
    pill.addEventListener('click', function() {
        categoryPills.forEach(p => p.classList.remove('active'));
        this.classList.add('active');
        activeCategory = this.getAttribute('data-category').toLowerCase().trim();
        runFilter();
    });
});

// จำลองการกดเลือกเมนูใส่ตะกร้า
let totalItems = 0;
function addToCart(name, price) {
    totalItems++;
    document.getElementById('cartCount').textContent = totalItems;

    Swal.fire({
        icon: 'success',
        title: 'เพิ่มลงตะกร้าแล้ว!',
        text: `${name} (ราคา ฿${price.toFixed(2)})`,
        timer: 1200,
        showConfirmButton: false,
        toast: true,
        position: 'top-end'
    });
}
</script>
</body>
</html>