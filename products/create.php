<?php
require_once '../includes/auth_check.php';
require_once '../config/db.php';

$errors = [];

// ดึงหมวดหมู่มาใส่ใน dropdown
$categories = $conn->query("SELECT * FROM categories")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $category_id = $_POST['category_id'] ?? '';
    $price = $_POST['price'] ?? '';
    $stock = $_POST['stock'] ?? '';

    // Validation (ตรวจสอบความถูกต้องของข้อมูล)
    if (empty($name)) {
        $errors[] = "Please enter the menu name";
    }
    if (empty($category_id)) {
        $errors[] = "Please select a category";
    }
    if (!is_numeric($price) || $price < 0) {
        $errors[] = "Price must be a number greater than or equal to 0";
    }
    if (!filter_var($stock, FILTER_VALIDATE_INT, ["options" => ["min_range" => 0]])) {
        $errors[] = "Stock must be a positive integer or 0";
    }

    // ถ้าไม่มี error ให้บันทึกลงฐานข้อมูล
    if (empty($errors)) {
        try {
            $stmt = $conn->prepare("INSERT INTO products (name, category_id, price, stock) VALUES (:name, :category_id, :price, :stock)");
            $stmt->execute([
                ':name' => $name,
                ':category_id' => $category_id,
                ':price' => $price,
                ':stock' => $stock
            ]);

            $_SESSION['success'] = "Added menu \"$name\" successfully!";
            header("Location: index.php");
            exit();
        } catch (PDOException $e) {
            $errors[] = "An error occurred while saving: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Menu - Tomodachi café</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="py-5">

<div class="container" style="max-width: 580px;">
    
    <div class="mb-4 d-flex align-items-center justify-content-between">
        <a href="index.php" class="text-decoration-none text-muted small">
            <i class="bi bi-chevron-left"></i> Back to Menu List
        </a>
        <span class="badge bg-white text-muted border rounded-pill px-3 py-2">New Item</span>
    </div>

    <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5">
        <h3 class="fw-normal mb-1">Add New Menu</h3>
        <p class="text-muted small mb-4">Add fresh coffee, drinks, or bakery items to the system</p>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger py-2 small rounded-3 border-0 mb-4" role="alert">
                <ul class="mb-0 ps-3">
                    <?php foreach ($errors as $err): ?>
                        <li><?= htmlspecialchars($err); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form action="create.php" method="POST" id="createForm">
            <div class="mb-3">
                <label for="name" class="form-label small text-muted">Menu Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control rounded-3 px-3 py-2 border-0 bg-light" id="name" name="name" required placeholder="e.g. Vanilla Latte, Matcha Cake" value="<?= htmlspecialchars($_POST['name'] ?? ''); ?>">
            </div>

            <div class="mb-3">
                <label for="category_id" class="form-label small text-muted">Category <span class="text-danger">*</span></label>
                <select class="form-select rounded-3 px-3 py-2 border-0 bg-light" id="category_id" name="category_id" required>
                    <option value="" disabled <?= empty($_POST['category_id']) ? 'selected' : ''; ?>>-- Select Category --</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat['id']; ?>" <?= (($_POST['category_id'] ?? '') == $cat['id']) ? 'selected' : ''; ?>>
                            <?= htmlspecialchars($cat['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label for="price" class="form-label small text-muted">Price (THB) <span class="text-danger">*</span></label>
                    <input type="number" step="0.25" min="0" class="form-control rounded-3 px-3 py-2 border-0 bg-light" id="price" name="price" required placeholder="0.00" value="<?= htmlspecialchars($_POST['price'] ?? ''); ?>">
                </div>
                <div class="col-md-6">
                    <label for="stock" class="form-label small text-muted">Stock Quantity <span class="text-danger">*</span></label>
                    <input type="number" min="0" class="form-control rounded-3 px-3 py-2 border-0 bg-light" id="stock" name="stock" required placeholder="10" value="<?= htmlspecialchars($_POST['stock'] ?? '10'); ?>">
                </div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn text-white rounded-pill px-4 py-2 flex-grow-1 shadow-sm" style="background-color: var(--cafe-primary); border: none; font-weight: 500;">
                    Save Data
                </button>
                <a href="index.php" class="btn btn-light rounded-pill px-4 py-2 border text-muted">
                    Cancel
                </a>
            </div>
        </form>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>