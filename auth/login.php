<?php
session_start();
// ถ้าล็อกอินค้างอยู่แล้ว ให้ไปหน้าจัดการสินค้าทันที
if (isset($_SESSION['user_id'])) {
    header("Location: ../products/index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เข้าสู่ระบบ - Cafe Management</title>
    <!-- Bootstrap 5 CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center justify-content-center min-vh-100">

<div class="card shadow-sm border-0" style="max-width: 400px; width: 100%;">
    <div class="card-body p-4">
        <h4 class="card-title text-center mb-3">☕ Cafe System</h4>
        <p class="text-muted text-center small mb-4">เข้าสู่ระบบเพื่อจัดการร้านคาเฟ่</p>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger py-2 small" role="alert">
                <?= htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <form action="login_action.php" method="POST">
            <div class="mb-3">
                <label for="username" class="form-label">ชื่อผู้ใช้ (Username)</label>
                <input type="text" class="form-control" id="username" name="username" required autofocus>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">รหัสผ่าน (Password)</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-primary w-100 py-2">เข้าสู่ระบบ</button>
        </form>
        
        <div class="mt-3 text-center text-muted small">
            บัญชีเริ่มต้น: <b>admin</b> / รหัสผ่าน: <b>admin123</b>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>