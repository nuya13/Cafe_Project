<?php
require_once '../includes/auth_check.php';
require_once '../config/db.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    header("Location: index.php");
    exit();
}

$categories = $conn->query("SELECT * FROM categories")->fetchAll();

// ดึงข้อมูลเดิมมาแสดง
$stmt = $conn->prepare("SELECT * FROM products WHERE id = :id LIMIT 1");
$stmt->execute([':id' => $id]);
$product = $stmt->fetch();

if (!$product) {
    $_SESSION['error'] = "No menu item found with ID $id.";
    header("Location: index.php");
    exit();
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $category_id = $_POST['category_id'] ?? '';
    $price = $_POST['price'] ?? '';
    $stock = $_POST['stock'] ?? '';

    // Validation
    if (empty($name)) $errors[] = "Please enter the menu name.";
    if (empty($category_id)) $errors[] = "Please select a category.";
    if (!is_numeric($price) || $price < 0) $errors[] = "Please enter a valid price.";
    if (!filter_var($stock, FILTER_VALIDATE_INT, ["options" => ["min_range" => 0]])) $errors[] = "Please enter a valid stock quantity.";

    if (empty($errors)) {
        try {
            $updateStmt = $conn->prepare("UPDATE products SET name = :name, category_id = :category_id, price = :price, stock = :stock WHERE id = :id");
            $updateStmt->execute([
                ':name' => $name,
                ':category_id' => $category_id,
                ':price' => $price,
                ':stock' => $stock,
                ':id' => $id
            ]);

            $_SESSION['success'] = "Updated menu \"$name\" successfully!";
            header("Location: index.php");
            exit();
        } catch (PDOException $e) {
            $errors[] = "An error occurred while updating the menu: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Menu Item - Cafe System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-5" style="max-width: 600px;">
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3">
            <h5 class="card-title mb-0">✏️ Edit Menu Item (#<?= $product['id']; ?>)</h5>
        </div>
        <div class="card-body p-4">

            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger py-2">
                    <ul class="mb-0 small ps-3">
                        <?php foreach ($errors as $err): ?>
                            <li><?= htmlspecialchars($err); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form action="edit.php?id=<?= $id; ?>" method="POST">
                <div class="mb-3">
                    <label for="name" class="form-label">Menu Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="name" name="name" required value="<?= htmlspecialchars($_POST['name'] ?? $product['name']); ?>">
                </div>

                <div class="mb-3">
                    <label for="category_id" class="form-label">Category <span class="text-danger">*</span></label>
                    <select class="form-select" id="category_id" name="category_id" required>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat['id']; ?>" <?= (($_POST['category_id'] ?? $product['category_id']) == $cat['id']) ? 'selected' : ''; ?>>
                                <?= htmlspecialchars($cat['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="price" class="form-label">Price (THB) <span class="text-danger">*</span></label>
                        <input type="number" step="0.25" min="0" class="form-control" id="price" name="price" required value="<?= htmlspecialchars($_POST['price'] ?? $product['price']); ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="stock" class="form-label">Stock Quantity <span class="text-danger">*</span></label>
                        <input type="number" min="0" class="form-control" id="stock" name="stock" required value="<?= htmlspecialchars($_POST['stock'] ?? $product['stock']); ?>">
                    </div>
                </div>

                <div class="d-flex justify-content-between mt-4">
                    <a href="index.php" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-warning px-4">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>