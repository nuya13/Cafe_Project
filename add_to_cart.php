<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'config/db.php';

if (isset($_POST['product_id']) && isset($_POST['product_name']) && isset($_POST['price'])) {
    $product_id = $_POST['product_id'];
    $product_name = $_POST['product_name'];
    $price = $_POST['price'];

    
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    // ถ้ามีสินค้านี้ในตะกร้าแล้ว ให้บวก1
    if (isset($_SESSION['cart'][$product_id])) {
        $_SESSION['cart'][$product_id]['quantity'] += 1;
    } else {
        
        $_SESSION['cart'][$product_id] = [
            'name' => $product_name,
            'price' => $price,
            'quantity' => 1
        ];
    }

    // ส่งจำนวนรวมในตะกร้า
    echo json_encode(['status' => 'success', 'total_items' => count($_SESSION['cart'])]);
}
?>