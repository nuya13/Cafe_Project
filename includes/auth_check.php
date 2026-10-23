<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ตรวจสอบว่าไม่ได้ล็อกอินทั้งแอดมิน (user_id) และลูกค้า (customer_id)
if (!isset($_SESSION['user_id']) && !isset($_SESSION['customer_id'])) {
    header("Location: index.php");
    exit();
}