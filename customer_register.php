<?php
session_start();
require_once 'config/db.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullname = trim($_POST['fullname'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!empty($fullname) && !empty($email) && !empty($password)) {
        // เช็คว่าอีเมลซ้ำไหม
        $stmt = $conn->prepare("SELECT id FROM customers WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $error = "อีเมลนี้ถูกใช้งานแล้ว";
        } else {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $insert = $conn->prepare("INSERT INTO customers (fullname, email, password) VALUES (?, ?, ?)");
            if ($insert->execute([$fullname, $email, $hashedPassword])) {
                $_SESSION['customer_id'] = $conn->lastInsertId();
                $_SESSION['customer_name'] = $fullname;
                header("Location: customer.php");
                exit;
            } else {
                $error = "เกิดข้อผิดพลาดในการลงทะเบียน";
            }
        }
    } else {
        $error = "กรุณากรอกข้อมูลให้ครบถ้วน";
    }
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Register - Tomodachi café</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="d-flex align-items-center justify-content-center min-vh-100 py-4">

<div class="container" style="max-width: 420px;">
    <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5">
        <div class="text-center mb-4">
            <span class="fs-1">☕</span>
            <h3 class="fw-normal mt-2 mb-1">สมัครสมาชิกลูกค้า</h3>
            <p class="text-muted small">ยินดีต้อนรับสู่ Tomodachi café</p>
        </div>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger py-2 small rounded-3 border-0 mb-3">
                <i class="bi bi-exclamation-circle me-1"></i> <?= htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="mb-3">
                <label class="form-label small text-muted">ชื่อ-นามสกุล / ชื่อเล่น</label>
                <input type="text" name="fullname" class="form-control rounded-pill px-3 py-2 border-0 bg-light" required placeholder="เช่น พิมลดา">
            </div>
            <div class="mb-3">
                <label class="form-label small text-muted">อีเมล (Email)</label>
                <input type="email" name="email" class="form-control rounded-pill px-3 py-2 border-0 bg-light" required placeholder="name@example.com">
            </div>
            <div class="mb-4">
                <label class="form-label small text-muted">รหัสผ่าน (Password)</label>
                <input type="password" name="password" class="form-control rounded-pill px-3 py-2 border-0 bg-light" required placeholder="ตั้งรหัสผ่าน">
            </div>

            <button type="submit" class="btn w-100 rounded-pill py-2 text-white shadow-sm" style="background-color: var(--cafe-primary); border: none; font-weight: 500;">
                สมัครสมาชิก
            </button>
        </form>

        <div class="text-center mt-3 small">
            มีบัญชีอยู่แล้ว? <a href="customer_login.php" class="text-decoration-none" style="color: var(--cafe-primary);">เข้าสู่ระบบ</a>
        </div>
    </div>
</div>

</body>
</html>