<?php
require_once '../includes/auth_check.php';
require_once '../config/db.php';

// ดึงรายการหมวดหมู่สำหรับตัวกรอง
$categories =$conn->query("SELECT * FROM categories")->fetchAll();

// ดึงรายการสินค้าทั้งหมด พร้อมชื่อหมวดหมู่ (ใช้ JOIN)
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
    <title>จัดการเมนูสินค้า - Cafe System</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- SweetAlert2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand fw-bold" href="index.php">☕ Cafe Management</a>
        <div class="d-flex align-items-center text-white">
            <span class="me-3 small">
                User: <b><?= htmlspecialchars($_SESSION['fullname']); ?></b> 
                <span class="badge bg-secondary"><?= htmlspecialchars($_SESSION['role']); ?></span>
            </span>
            <a href="../auth/logout.php" class="btn btn-outline-danger btn-sm">Logout</a>
        </div>
    </div>
</nav>

<div class="container py-4">

    <!-- แจ้งเตือนสถานะสำเร็จ/ล้มเหลว ผ่าน Session -->
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
        <h3 class="mb-0">📋Drink and Bakery Menu</h3>
        <a href="create.php" class="btn btn-primary">
            <i class="bi bi-plus-circle me-1"></i> Add New Menu
        </a>
    </div>

    <!-- ส่วนค้นหาและตัวกรอง (JavaScript Interactivity) -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-2">
                <div class="col-md-8">
                    <input type="text" id="searchInput" class="form-control" placeholder="🔍 Search menu items...">
                </div>
                <div class="col-md-4">
                    <select id="categoryFilter" class="form-select">
                        <option value="">All Categories</option>
                        <?php foreach ($categories as$cat): ?>
                            <option value="<?= htmlspecialchars($cat['name']); ?>"><?= htmlspecialchars($cat['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- ตารางแสดงสินค้า -->
    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" id="productsTable">
                <thead class="table-light">
                    <tr>
                        <th style="width: 80px;">รหัส (ID)</th>
                        <th>ชื่อเมนู (Name)</th>
                        <th>หมวดหมู่ (Category)</th>
                        <th class="text-end">ราคา (บาท) (Price)</th>
                        <th class="text-center">สต็อก (ชิ้น/แก้ว) (Stock)</th>
                        <th class="text-center" style="width: 150px;">จัดการ (Actions)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($products) > 0): ?>
                        <?php foreach ($products as$p): ?>
                            <tr class="product-row">
                                <td class="text-muted">#<?= $p['id']; ?></td>
                                <td class="fw-semibold product-name"><?= htmlspecialchars($p['name']); ?></td>
                                <td>
                                    <span class="badge bg-info text-dark product-category">
                                        <?= htmlspecialchars($p['category_name'] ?? 'ไม่มีหมวดหมู่'); ?>
                                    </span>
                                </td>
                                <td class="text-end fw-bold"><?= number_format($p['price'], 2); ?></td>
                                <td class="text-center">
                                    <?php if ($p['stock'] > 5): ?>
                                        <span class="badge bg-success"><?= $p['stock']; ?></span>
                                    <?php elseif ($p['stock'] > 0): ?>
                                        <span class="badge bg-warning text-dark"><?= $p['stock']; ?> (ใกล้หมด)</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">หมดสต็อก</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <a href="edit.php?id=<?= $p['id']; ?>" class="btn btn-sm btn-outline-warning me-1">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <!-- ปุ่มลบจะเรียกฟังก์ชัน confirmDelete ใน JS -->
                                    <button class="btn btn-sm btn-outline-danger" onclick="confirmDelete(<?= $p['id']; ?>, '<?= htmlspecialchars($p['name']); ?>')">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr id="noDataRow">
                            <td colspan="6" class="text-center py-4 text-muted">No menu items available. Click "Add New Menu" to get started.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- Bootstrap 5 JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<!-- SweetAlert2 JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
// 1. ฟังก์ชัน Real-time Search & Filter ด้วย JavaScript
const searchInput = document.getElementById('searchInput');
const categoryFilter = document.getElementById('categoryFilter');
const tableRows = document.querySelectorAll('.product-row');

function filterProducts() {
    const searchTerm = searchInput.value.toLowerCase().trim();
    const selectedCategory = categoryFilter.value.toLowerCase().trim();

    tableRows.forEach(row => {
        const productName = row.querySelector('.product-name').textContent.toLowerCase();
        const productCategory = row.querySelector('.product-category').textContent.toLowerCase();

        const matchName = productName.includes(searchTerm);
        const matchCategory = selectedCategory === '' || productCategory.includes(selectedCategory);

        if (matchName && matchCategory) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}

searchInput.addEventListener('keyup', filterProducts);
categoryFilter.addEventListener('change', filterProducts);

// 2. ฟังก์ชันยืนยันก่อนลบสินค้าด้วย SweetAlert2
function confirmDelete(id, name) {
    Swal.fire({
        title: 'Confirm Menu Deletion?',
        text: `Are you sure you want to delete "${name}" from the system?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Delete',
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