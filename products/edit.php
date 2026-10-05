<?php
require_once '../includes/auth_check.php';
require_once '../config/db.php';

$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: index.php");
    exit;
}

// ดึงข้อมูลสินค้าที่ต้องการแก้ไข
$stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch();

if (!$product) {
    header("Location: index.php");
    exit;
}

// ดึงหมวดหมู่
$categories = $conn->query("SELECT * FROM categories")->fetchAll();

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $category_id = $_POST['category_id'] ?? null;
    $price = $_POST['price'] ?? 0;
    $stock = $_POST['stock'] ?? 0;

    if (!empty($name) && !empty($category_id) && $price >= 0 && $stock >= 0) {
        $updateStmt = $conn->prepare("UPDATE products SET name = ?, category_id = ?, price = ?, stock = ? WHERE id = ?");
        $updateStmt->execute([$name, $category_id, $price, $stock, $id]);
        header("Location: index.php");
        exit;
    } else {
        $error = "Please fill in all required fields correctly.";
    }
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Menu - Purr'Coffee</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="py-5">

<div class="container" style="max-width: 580px;">
    
    <div class="mb-4 d-flex align-items-center justify-content-between">
        <a href="index.php" class="text-decoration-none text-muted small">
            <i class="bi bi-chevron-left"></i> กลับหน้ารายการเมนู
        </a>
        <span class="badge bg-white text-muted border rounded-pill px-3 py-2">ID: #<?= $product['id']; ?></span>
    </div>

    <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5">
        <h3 class="fw-normal mb-1">แก้ไขเมนูสินค้า</h3>
        <p class="text-muted small mb-4">ปรับปรุงรายละเอียด ราคา และจำนวนสต็อก</p>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger py-2 small rounded-3 border-0 mb-4" role="alert">
                <i class="bi bi-exclamation-circle me-1"></i> <?= htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="mb-3">
                <label class="form-label small text-muted">ชื่อเมนูสินค้า</label>
                <input type="text" name="name" class="form-control rounded-3 px-3 py-2 border-0 bg-light" value="<?= htmlspecialchars($product['name']); ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label small text-muted">หมวดหมู่</label>
                <select name="category_id" class="form-select rounded-3 px-3 py-2 border-0 bg-light" required>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat['id']; ?>" <?= ($cat['id'] == $product['category_id']) ? 'selected' : ''; ?>>
                            <?= htmlspecialchars($cat['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label small text-muted">ราคา (บาท)</label>
                    <input type="number" step="0.01" min="0" name="price" class="form-control rounded-3 px-3 py-2 border-0 bg-light" value="<?= $product['price']; ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label small text-muted">จำนวนในสต็อก</label>
                    <input type="number" min="0" name="stock" class="form-control rounded-3 px-3 py-2 border-0 bg-light" value="<?= $product['stock']; ?>" required>
                </div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn text-white rounded-pill px-4 py-2 flex-grow-1 shadow-sm" style="background-color: var(--cafe-primary); border: none; font-weight: 500;">
                    บันทึกการเปลี่ยนแปลง
                </button>
                <a href="index.php" class="btn btn-light rounded-pill px-4 py-2 border text-muted">
                    ยกเลิก
                </a>
            </div>
        </form>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>