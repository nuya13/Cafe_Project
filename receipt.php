<?php
session_start();
require_once 'config/db.php';

// ตรวจสอบว่ามี order_id ส่งมาไหม
if (!isset($_GET['order_id'])) {
    header("Location: customer.php");
    exit();
}

$order_id = $_GET['order_id'];

$stmt = $conn->prepare("SELECT o.*, c.fullname AS customer_name FROM orders o 
                        JOIN customers c ON o.customer_id = c.id 
                        WHERE o.order_id = ?");
$stmt->execute([$order_id]);
$order = $stmt->fetch();

if (!$order) {
    echo "ไม่พบข้อมูลคำสั่งซื้อนี้";
    exit();
}

// ดึงรายการสินค้าในบิล
$stmt_items = $conn->prepare("SELECT oi.*, p.name AS product_name FROM order_items oi 
                             JOIN products p ON oi.product_id = p.id 
                             WHERE oi.order_id = ?");
$stmt_items->execute([$order_id]);
$order_items = $stmt_items->fetchAll();
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>ใบเสร็จรับเงิน - Tomodachi Café</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="py-5">
    <div class="container">
        
        <div class="cafe-card-container p-4 p-md-5 mx-auto" style="max-width: 650px;">
            
            <div class="text-center receipt-header">
                <h2 class="fw-bold mb-1" style="color: var(--cafe-text-dark);">☕ Tomodachi Café</h2>
                <p class="text-muted mb-0">ใบเสร็จรับเงิน / Receipt</p>
            </div>
            
            <div class="mb-4 small" style="color: var(--cafe-text-dark);">
                <p class="mb-2"><strong>เลขที่ใบเสร็จ (ID):</strong> #<?php echo $order['order_id']; ?></p>
                <p class="mb-2"><strong>ชื่อลูกค้า (Name):</strong> <?php echo htmlspecialchars($order['customer_name']); ?></p>
                <p class="mb-2"><strong>วันที่-เวลา (Date-Time):</strong> <?php echo $order['created_at']; ?></p>
                <p class="mb-0"><strong>สถานะการชำระเงิน (Payment Status):</strong> 
                    <span class="<?php echo ($order['payment_status'] == 'Paid') ? 'badge-cafe-paid' : 'badge-cafe-pending'; ?>">
                        <?php echo $order['payment_status']; ?>
                    </span>
                </p>
            </div>

            
            <div class="table-responsive mb-4">
                <table class="table table-cafe mb-0">
                    <thead>
                        <tr>
                            <th>สินค้า</th>
                            <th class="text-center">จำนวน</th>
                            <th class="text-end">ราคา/หน่วย</th>
                            <th class="text-end">รวม</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($order_items as $item): ?>
                            <tr>
                                <td><?= htmlspecialchars($item['product_name']); ?></td>
                                <td class="text-center"><?= $item['quantity']; ?></td>
                                <td class="text-end">฿<?= number_format($item['price'], 2); ?></td>
                                <td class="text-end">฿<?= number_format($item['price'] * $item['quantity'], 2); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="3" class="text-end pt-3">ราคารวมสุทธิ:</th>
                            <th class="text-end pt-3 text-danger fs-5">฿<?php echo number_format($order['total_price'], 2); ?></th>
                        </tr>
                    </tfoot>
                </table>
            </div>

            
            <div class="text-center d-flex justify-content-center gap-3 mt-4">
                <a href="customer.php" class="btn btn-cafe-primary text-decoration-none">
                    <i class="bi bi-house-door me-1"></i> กลับสู่หน้าหลักเมนู
                </a>
                <button onclick="window.print()" class="btn btn-cafe-outline">
                    <i class="bi bi-printer me-1"></i> พิมพ์ใบเสร็จ
                </button>
            </div>
            
        </div>
    </div>
</body>
</html>