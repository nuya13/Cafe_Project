<?php
// เริ่ม Session หากยังไม่ได้เริ่ม
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ตรวจสอบว่าไม่ได้ล็อกอินทั้งแอดมิน (user_id) และลูกค้า (customer_id)
if (!isset($_SESSION['user_id']) && !isset($_SESSION['customer_id'])) {
    // ถ้าไม่มี Session ไหนเลย ให้กลับไปหน้าเข้าสู่ระบบหลัก
    header("Location: index.php");
    exit();
}