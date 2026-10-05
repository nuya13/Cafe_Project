<?php
session_start();
require_once 'config/db.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!empty($email) && !empty($password)) {
        $stmt = $conn->prepare("SELECT * FROM customers WHERE email = ?");
        $stmt->execute([$email]);
        $customer = $stmt->fetch();

        if ($customer && password_verify($password, $customer['password'])) {
            $_SESSION['customer_id'] = $customer['id'];
            $_SESSION['customer_name'] = $customer['fullname'];
            header("Location: customer.php");
            exit;
        } else {
            $error = "อีเมลหรือรหัสผ่านไม่ถูกต้อง";
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
    <title>Customer Login - Tomodachi café</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="d-flex align-items-center justify-content-center min-vh-100 py-4">

<div class="container" style="max-width: 420px;">
    <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5">
        <div class="text-center mb-4">
            <span class="fs-1">☕</span>
            <h3 class="fw-normal mt-2 mb-1">เข้าสู่ระบบสมาชิก</h3>
            <p class="text-muted small">Tomodachi café Digital Menu</p>
        </div>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger py-2 small rounded-3 border-0 mb-3">
                <i class="bi bi-exclamation-circle me-1"></i> <?= htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="mb-3">
                <label class="form-label small text-muted">อีเมล (Email)</label>
                <input type="email" name="email" class="form-control rounded-pill px-3 py-2 border-0 bg-light" required placeholder="name@example.com">
            </div>
            <div class="mb-4">
                <label class="form-label small text-muted">รหัสผ่าน (Password)</label>
                <input type="password" name="password" class="form-control rounded-pill px-3 py-2 border-0 bg-light" required placeholder="รหัสผ่านของคุณ">
            </div>

            <button type="submit" class="btn w-100 rounded-pill py-2 text-white shadow-sm" style="background-color: var(--cafe-primary); border: none; font-weight: 500;">
                เข้าสู่ระบบ
            </button>
        </form>

        <div class="text-center mt-3 small">
            ยังไม่มีบัญชีสมาชิก? <a href="customer_register.php" class="text-decoration-none" style="color: var(--cafe-primary);">สมัครสมาชิก</a>
        </div>

        <div class="text-center mt-4 pt-3 border-top" style="border-color: var(--cafe-border) !important;">
            <a href="customer.php" class="text-decoration-none small text-muted">
                <i class="bi bi-arrow-left"></i> ดูเมนูโดยไม่เข้าสู่ระบบ
            </a>
        </div>
    </div>
</div>

</body>
</html>