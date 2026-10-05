<?php
require_once '../includes/auth_check.php';
require_once '../config/db.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if ($id) {
    try {
        $stmt = $conn->prepare("DELETE FROM products WHERE id = :id");
        $stmt->execute([':id' => $id]);

        $_SESSION['success'] = "ลบรายการสินค้าเรียบร้อยแล้ว";
    } catch (PDOException $e) {
        $_SESSION['error'] = "ไม่สามารถลบข้อมูลได้: " . $e->getMessage();
    }
}

header("Location: index.php");
exit();
?>