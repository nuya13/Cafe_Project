<?php
// เริ่ม Session หากยังไม่ได้เริ่ม
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ตรวจสอบว่ามีข้อมูลการล็อกอินของฝั่งพนักงาน/แอดมินหรือไม่
if (!isset($_SESSION['user_id'])) {
    // ถ้ายังไม่ได้ล็อกอิน ให้ส่งกลับไปหน้า Login ทันที
    header("Location: ../auth/login.php");
    exit();
}