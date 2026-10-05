<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ตรวจสอบว่าล็อกอินหรือยัง ถ้ายังไม่ล็อกอินให้ดีดกลับไปหน้า login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}
?>