<?php
require_once '../includes/auth_check.php';
require_once '../config/db.php';

// Retrieve all data from categories and products tables
$categories =$conn->query("SELECT * FROM categories")->fetchAll();

// Retrieve all data from products table with category names
$sql = "SELECT p.*, c.name AS category_name 
        FROM products p 
        LEFT JOIN categories c ON p.category_id = c.id 
        ORDER BY p.id DESC";
$stmt = $conn->query($sql);
$products =$stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tomodachi café Menu - Cafe Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar ซ้ายตามแบบภาพเป๊ะๆ -->
        <aside class="col-md-3 col-lg-2 sidebar p-4 d-flex flex-column justify-content-between">
            <div>
                <div class="brand-title mb-4 d-flex align-items-center gap-2">
                    <span>☕</span> Tomodachi café
                </div>

                <div class="d-flex flex-column gap-2">
                    <a href="index.php" class="nav-link-custom active">
                        <i class="bi bi-grid-fill"></i> Coffee Menu
                    </a>
                    <a href="create.php" class="nav-link-custom">
                        <i class="bi bi-plus-circle"></i> Add Menu
                    </a>
                </div>
            </div>

            <!-- กล่องข้อมูลผู้ใช้และปุ่ม Logout ด้านล่าง -->
            <div class="pt-3 border-top" style="border-color: var(--cafe-border) !important;">
                <div class="small fw-bold text-truncate"><?= htmlspecialchars($_SESSION['fullname']); ?></div>
                <div class="badge bg-secondary mb-2"><?= htmlspecialchars($_SESSION['role']); ?></div>
                <a href="../auth/logout.php" class="nav-link-custom text-danger px-0">
                    <i class="bi bi-box-arrow-left"></i> Log out
                </a>
            </div>
        </aside>

        <!-- Main Content ขวามือ -->
        <main class="col-md-9 col-lg-10 p-4 p-md-5">

            <!-- แถบค้นหาด้านบน -->
            <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
                <div class="flex-grow-1" style="max-width: 450px;">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-0 rounded-start-pill ps-3 text-muted">
                            <i class="bi bi-search"></i>
                        </span>
                        <input type="text" id="searchInput" class="form-control border-0 rounded-end-pill py-2" placeholder="Search menu...">
                    </div>
                </div>

                <a href="create.php" class="btn btn-warning text-white fw-bold px-4 py-2 rounded-pill shadow-sm" style="background-color: var(--cafe-primary); border: none;">
                    <i class="bi bi-plus-lg me-1"></i> Add Menu
                </a>
            </div>

            <!-- หัวข้อหน้าและปุ่มหมวดหมู่ (Category Pills) -->
            <h2 class="fw-bold mb-3">Coffee menu</h2>
            <p class="text-muted mb-4">Manage your menu, check stock levels, and update each item quickly from one simple dashboard.</p>

            <div class="d-flex flex-wrap gap-2 mb-4" id="categoryPills">
                <button type="button" class="category-pill active" data-category="">All</button>
                <?php foreach ($categories as$cat): ?>
                    <button type="button" class="category-pill" data-category="<?= htmlspecialchars($cat['name']); ?>">
                        <?= htmlspecialchars($cat['name']); ?>
                    </button>
                <?php endforeach; ?>
            </div>

            <!-- Grid แสดงการ์ดสินค้า -->
            <div class="row g-4" id="productGrid">
    <?php if (count($products) > 0): ?>
        <?php foreach ($products as $p): ?>
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
                        <div class="btn-group">
                            <a href="edit.php?id=<?= $p['id']; ?>" class="btn btn-outline-secondary btn-sm btn-cafe-action">
                                <i class="bi bi-pencil"></i> Edit
                            </a>
                            <button onclick="confirmDelete(<?= $p['id']; ?>, '<?= htmlspecialchars($p['name']); ?>')" class="btn btn-outline-danger btn-sm btn-cafe-action">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>

            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="col-12 text-center py-5 text-muted">
            <div class="fw-medium">Do not have any products</div>
            <small class="d-block mt-2">Please add a new menu to start managing your food and beverage items</small>
        </div>
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
        const name = item.getAttribute('data-name');
        const category = item.getAttribute('data-category');

        const matchName = name.includes(searchTerm);
        const matchCategory = (activeCategory === '') || category.includes(activeCategory);

        if (matchName && matchCategory) {
            item.style.display = '';
        } else {
            item.style.display = 'none';
        }
    });
}

searchInput.addEventListener('input', runFilter);

// กดสลับ Category Pill
categoryPills.forEach(pill => {
    pill.addEventListener('click', function() {
        categoryPills.forEach(p => p.classList.remove('active'));
        this.classList.add('active');
        activeCategory = this.getAttribute('data-category').toLowerCase().trim();
        runFilter();
    });
});

// SweetAlert2 ยืนยันการลบ
function confirmDelete(id, name) {
    Swal.fire({
        title: 'Are you sure?',
        text: `You want to delete "${name}" from the system?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#D97745',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Delete Data',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = `delete.php?id=${id}`;
        }
    });
}
</script>
</body>
</html>