<?php
session_start();
require_once 'config.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: index.php");
    exit();
}
$product_id = $_GET['id'];

$stmt = $conn->prepare("SELECT p.*, c.category_name
        FROM products p
        LEFT JOIN categories c ON p.category_id = c.category_id
        WHERE p.product_id = ?");
$stmt->execute([$product_id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

// ตรวจสอบว่าผู้ใช้เข้าสู่ระบบแล้วหรือไม่
$isLoggedIn = isset($_SESSION['user_id']);

if (!$product) {
echo "<h3>ไมพ่ บสนิ คำ้ทคี่ ณุ ตอ้ งกำร</h3>";
exit;
}
$img = !empty($product['image'])
? 'product_images/' . rawurlencode($product['image'])
: 'product_images/no-image.jpg';
?>
<!DOCTYPE html>
<html lang="th">
    <head>
        <meta charset="UTF-8">
        <title>รายละเอยีดสิน ค้า</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    </head>
    <style>
        /* ====== พื้นหลังและฟอนต์หลัก ====== */
body {
    background: linear-gradient(135deg, #e8f5e9 0%, #c8e6c9 100%);
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    min-height: 100vh;
}

/* ====== หัวข้อหลัก ====== */
h1, h2, h3 {
    color: #2e7d32;
    font-weight: 600;
}

/* ====== การ์ดสินค้า / การ์ดรายละเอียด ====== */
.card {
    border-radius: 12px;
    box-shadow: 0 6px 18px rgba(0,0,0,0.08);
    border: 1px solid #a5d6a7;
    transition: transform 0.2s ease;
}
.card:hover {
    transform: translateY(-5px);
}

/* ชื่อสินค้า */
.card-title {
    color: #388e3c;
    font-weight: 600;
}

/* ====== ปุ่มหลัก (เขียว) ====== */
.btn-success {
    background-color: #4caf50 !important;
    border: none !important;
}
.btn-success:hover {
    background-color: #388e3c !important;
}

/* ปุ่มเส้นขอบเขียว */
.btn-outline-primary {
    border-color: #4caf50 !important;
    color: #388e3c !important;
}
.btn-outline-primary:hover {
    background-color: #4caf50 !important;
    color: white !important;
}

/* ปุ่มเหลือง (ตะกร้า) */
.btn-warning {
    background-color: #ffb74d !important;
    border: none !important;
}
.btn-warning:hover {
    background-color: #f57c00 !important;
}

/* ปุ่มเขียวอ่อน (ออกจากระบบ) */
.btn-secondary {
    background-color: #81c784 !important;
    border: none !important;
}
.btn-secondary:hover {
    background-color: #66bb6a !important;
}

/* ====== Alert (ข้อความแจ้งเตือน) ====== */
.alert-info {
    background: rgba(129, 199, 132, 0.15) !important;
    color: #2e7d32 !important;
    border-left: 4px solid #4caf50 !important;
}

    </style>
    <body class="container mt-4">
        <a href="index.php" class="btn btn-secondary mb-3">← กลับหน้ารายการสินค้า</a>
        <div class="card">
            <div class="card-body">
                <h3 class="card-title"><?= htmlspecialchars($product['product_name'])?></h3>
                <h6 class="text-muted">หมวดหมู่: <?= htmlspecialchars($product['category_name'])?></h6>
                <p class="card-text mt-3"></p>
                <img src="<?= $img?>"
                <p><strong>ราคา:</strong> <?= htmlspecialchars($product['price'])?>บาท</p>
                <p><strong>คงเหลือ:</strong>  <?= htmlspecialchars($product['stock'])?>ชิ้น </p>
                <?php if ($isLoggedIn): ?>
                    <form action="cart.php" method="post" class="mt-3">
                        <input type="hidden" name="product_id" value="<?= $product['product_id'] ?>">
                        <label for="quantity">จำนวน:</label>
                        <input type="number" name="quantity" id="quantity" value="1" min="1" max="<?=
$product['stock'] ?>" required>
<button type="submit" class="btn btn-success">เพิ่มในตะกร้า</button>
</form>
<?php else: ?>
    <div class="alert alert-info mt-3">กรุณาเข้าสู่ระบบเพื่อเข้าซื้อสินค้า</div>
    <?php endif; ?>
</div>
</div>
</body>
</html>