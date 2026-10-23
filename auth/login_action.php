<?php
session_start();
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    // Input Validation
    if (empty($username) || empty($password)) {
        $_SESSION['error'] = "กรุณากรอกชื่อผู้ใช้และรหัสผ่านให้ครบถ้วน";
        header("Location: login.php");
        exit();
    }

    try {
        // ใช้ Prepared Statement เพื่อป้องกัน SQL Injection
        $stmt = $conn->prepare("SELECT id, username, password, fullname, role FROM users WHERE username = :username LIMIT 1");
        $stmt->execute([':username' => $username]);
        $user = $stmt->fetch();

        // ตรวจสอบว่ามีผู้ใช้ และรหัสผ่านตรงกับ Hash หรือไม่
        if ($user && password_verify($password, $user['password'])) {
            session_regenerate_id(true);

            
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['fullname'] = $user['fullname'];
            $_SESSION['role'] = $user['role'];

            
            header("Location: ../products/index.php");
            exit();
        } else {
            $_SESSION['error'] = "ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง";
            header("Location: login.php");
            exit();
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = "เกิดข้อผิดพลาดของระบบ กรุณาลองใหม่อีกครั้ง";
        header("Location: login.php");
        exit();
    }
} else {
    header("Location: login.php");
    exit();
}