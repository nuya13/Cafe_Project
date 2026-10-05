<?php
session_start();
require_once '../config/db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (!empty($username) && !empty($password)) {
        $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['fullname'] = $user['fullname'];
            $_SESSION['role'] = $user['role'];

            header("Location: ../products/index.php");
            exit;
        } else {
            $error = "Username or password is incorrect.";
        }
    } else {
        $error = "Please fill in all required fields.";
    }
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Login - Purr'Coffee</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="d-flex align-items-center justify-content-center min-vh-100 py-4">

<div class="container" style="max-width: 420px;">
    <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5">
        
        <div class="text-center mb-4">
            <span class="fs-1">☕</span>
            <h3 class="fw-normal mt-2 mb-1" style="color: var(--cafe-text-dark);">Tomodachi café</h3>
            <p class="text-muted small">System for staff management</p>
        </div>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger py-2 small rounded-3 border-0" role="alert">
                <i class="bi bi-exclamation-circle me-1"></i> <?= htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="mb-3">
                <label class="form-label small text-muted">Username</label>
                <input type="text" name="username" class="form-control rounded-pill px-3 py-2 border-0 bg-light" placeholder="Enter username" required>
            </div>

            <div class="mb-4">
                <label class="form-label small text-muted">รหัสผ่าน (Password)</label>
                <input type="password" name="password" class="form-control rounded-pill px-3 py-2 border-0 bg-light" placeholder="กรอกรหัสผ่าน" required>
            </div>

            <button type="submit" class="btn w-100 rounded-pill py-2 text-white shadow-sm" style="background-color: var(--cafe-primary); border: none; font-weight: 500;">
                เข้าสู่ระบบ
            </button>
        </form>

        <div class="text-center mt-4 pt-3 border-top" style="border-color: var(--cafe-border) !important;">
            <a href="../customer.php" class="text-decoration-none small text-muted">
                <i class="bi bi-arrow-left"></i> Back to Store (Customer View)
            </a>
        </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>