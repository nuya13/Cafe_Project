<?php
session_start();

// ถ้าล็อกอินอยู่แล้วให้พาไปหน้าสินค้า ถ้ายังไม่ล็อกอินให้ไปหน้าล็อกอิน
if (isset($_SESSION['user_id'])) {
    header("Location: products/index.php");
} else {
    header("Location: auth/login.php");
}
exit();
?>