<?php
session_start();

// ถ้าล็อกอินอยู่แล้วให้พาไปหน้าเมนูสินค้า ถ้ายังไม่ล็อกอินให้ไปหน้าล็อกอิน
if (isset($_SESSION['customer_id'])) {
    header("Location: customer.php");
} else {
    header("Location: customer_login.php");
}
exit();
?>