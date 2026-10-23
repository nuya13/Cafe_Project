<?php
session_start();

require_once 'config/db.php'; 

// ตรวจสอบว่าลูกค้าล็อกอินหรือยัง
if (!isset($_SESSION['customer_id'])) {
    header("Location: customer_login.php");
    exit();
}

// ตรวจสอบว่ามีสินค้าในตะกร้าไหม
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    echo "<script>alert('ไม่มีสินค้าในตะกร้า'); window.location='customer.php';</script>";
    exit();
}

$customer_id = $_SESSION['customer_id'];
$total_price = 0;

// คำนวณราคา
foreach ($_SESSION['cart'] as $item) {
    $total_price += $item['price'] * $item['quantity'];
}

try {
    
    $conn->beginTransaction();

    // บันทึกข้อมูลลงตาราง orders 
    $stmt = $conn->prepare("INSERT INTO orders (customer_id, total_price, payment_status) VALUES (?, ?, 'Paid')");
    $stmt->execute([$customer_id, $total_price]);
    
    // ดึง order_id ล่าสุดที่เพิ่งบันทึก
    $order_id = $conn->lastInsertId();

    // บันทึกรายการสินค้าแต่ละตัวลงตาราง
    $stmt_item = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
    
    foreach ($_SESSION['cart'] as $product_id => $item) {
        $stmt_item->execute([
            $order_id, 
            $product_id, 
            $item['quantity'], 
            $item['price']
        ]);
    }

    
    $conn->commit();

    
    unset($_SESSION['cart']);

    // พาไปหน้าใบเสร็จ 
    header("Location: receipt.php?order_id=" . $order_id);
    exit();

} catch (Exception $e) {
    
    $conn->rollBack();
    echo "เกิดข้อผิดพลาดในการสั่งซื้อ: " . $e->getMessage();
}
?>