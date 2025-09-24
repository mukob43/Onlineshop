<?php
require '../config.php';
require 'auth_admin.php';

// ตรวจสอบว่าได้ส่ง id สินค้ามาหรือไม่
if (!isset($_GET['id'])) {
    header("Location: products.php");
    exit;
}

$product_id = $_GET['id'];

// ดึงข้อมูลสินค้า
$stmt = $conn->prepare("SELECT * FROM products WHERE product_id = ?");
$stmt->execute([$product_id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    echo "<h3>ไม่พบข้อมูลสินค้า</h3>";
    exit;
}

// ดึงหมวดหมู่ทั้งหมด
$categories = $conn->query("SELECT * FROM categories")->fetchAll(PDO::FETCH_ASSOC);
// เมื่อมีการส่งฟอร์ม
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // เมื่อมีการส่งฟอร์ม
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['product_name']);
            $description = trim($_POST['description']);
            $price = isset($_POST['price']) ? (float)$_POST['price'] : 0;
            $stock = isset($_POST['stock']) ? (int)$_POST['stock'] : 0;
            $category_id = isset($_POST['category_id']) ? (int)$_POST['category_id'] : null;
        // ค่ารูปเดิมจากฟอร์ม
        $oldImage = $_POST['old_image'] ?? null;
        $removeImage = !empty($_POST['remove_image']); // true/false

        // เตรียมตัวแปรรูปที่จะบันทึก
        $newImageName = $oldImage; // default: คงรูปเดิมไว้

        // 1) ถ้ามีติ๊ก "ลบรูปเดิม" → ตั้งให้เป็น null
        if ($removeImage) {
            $newImageName = null;
        }

        // 2) ถ้ามีอัปโหลดไฟล์ใหม่ → ตรวจแล้วเซฟไฟล์และตั้งชื่อใหม่ทับค่า
        if (!empty($_FILES['product_image']['name'])) {
            $file = $_FILES['product_image'];
            // ตรวจชนิดไฟล์แบบง่าย (แนะนำ: ตรวจ MIME จริงด้วย finfo)
            $allowed = ['image/jpeg', 'image/png'];
            if (in_array($file['type'], $allowed, true) && $file['error'] === UPLOAD_ERR_OK) {
                $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                $newImageName = 'product_' . time() . '.' . $ext;
                $uploadDir = realpath(__DIR__ . '/../product_images');
                $destPath = $uploadDir . DIRECTORY_SEPARATOR . $newImageName;
                // ย้ายไฟล์อัปโหลด
                if (!move_uploaded_file($file['tmp_name'], $destPath)) {
                    // ถ้าย้ายไม่ได้ คงใช้รูปเดิมไว้
                    $newImageName = $oldImage;
                }
            }
        }
    }
            // อัปเดต DB
            $sql = "UPDATE products SET product_name = ?, description = ?, price = ?, stock = ?, category_id = ?, image = ?
            WHERE product_id = ?";
            $args = [$name, $description, $price, $stock, $category_id, $newImageName, $product_id];
            $stmt = $conn->prepare($sql);
            $stmt->execute($args);
            // ลบไฟล์เก่ำในดิสก์ ถ ้ำ:
// - มีรูปเดิม ($oldImage) และ
// - เกดิ กำรเปลยี่ นรปู (อัปโหลดใหมห่ รอื สั่งลบรปู เดมิ)
if (!empty($oldImage) && $oldImage !== $newImageName) {
    $baseDir = realpath(__DIR__ . '/../product_images');
    $filePath = realpath($baseDir . DIRECTORY_SEPARATOR . $oldImage);
    if ($filePath && strpos($filePath, $baseDir) === 0 && is_file($filePath)) {
        @unlink($filePath);
    }
}
header("Location: products.php");
exit;

}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>แก้ไขสินค้า</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #f8fffe 0%, #e8f5f0 100%);
            min-height: 100vh;
        }
        
        .main-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(25, 135, 84, 0.1);
            padding: 2rem;
            margin: 2rem auto;
            max-width: 1000px;
        }
        
        .page-header {
            background: linear-gradient(135deg, #198754, #20c997);
            color: white;
            padding: 1.5rem;
            border-radius: 10px;
            margin-bottom: 2rem;
            text-align: center;
        }
        
        .form-container {
            background: #f8fff9;
            border-radius: 10px;
            padding: 1.5rem;
        }
        
        .form-label {
            color: #146c43;
            font-weight: 600;
        }
        
        .form-control, .form-select {
            border: 2px solid #e9ecef;
            border-radius: 8px;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: #198754;
            box-shadow: 0 0 0 0.2rem rgba(25, 135, 84, 0.25);
        }
        
        .btn-success {
            background: #198754;
            border: none;
            border-radius: 25px;
            padding: 0.75rem 2rem;
            font-weight: 600;
        }
        
        .btn-secondary {
            background: #6c757d;
            border: none;
            border-radius: 25px;
        }
        
        .current-image {
            border: 2px solid #198754;
            border-radius: 8px;
        }
    </style>
</head>
<body>
    <div class="main-container">
        <div class="page-header">
            <h2>แก้ไขสินค้า</h2>
        </div>
        
        <a href="products.php" class="btn btn-secondary mb-3">← กลับไปยังรายการสินค้า</a>
        
        <div class="form-container">
            <form method="post" enctype="multipart/form-data" class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">ชื่อสินค้า</label>
                    <input type="text" name="product_name" class="form-control" value="<?= htmlspecialchars($product['product_name']) ?>" required>
                </div>
                
                <div class="col-md-3">
                    <label class="form-label">ราคา</label>
                    <input type="number" step="0.01" name="price" class="form-control" value="<?= $product['price'] ?>" required>
                </div>
                
                <div class="col-md-3">
                    <label class="form-label">จำนวนในคลัง</label>
                    <input type="number" name="stock" class="form-control" value="<?= $product['stock'] ?>" required>
                </div>
                
                <div class="col-md-6">
                    <label class="form-label">หมวดหมู่</label>
                    <select name="category_id" class="form-select" required>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat['category_id'] ?>" <?= ($cat['category_id'] == $product['category_id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($cat['category_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="col-12">
                    <label class="form-label">รายละเอียดสินค้า</label>
                    <textarea name="description" class="form-control" rows="4"><?= htmlspecialchars($product['description']) ?></textarea>
                </div>

                <div class="col-md-6">
                    <label class="form-label d-block">รูปปัจจุบัน</label>
                    <?php if (!empty($product['image'])): ?>
                        <img src="../product_images/<?= htmlspecialchars($product['image']) ?>" width="120" height="120" class="current-image mb-2">
                    <?php else: ?>
                        <span class="text-muted d-block mb-2">ไม่มีรูป</span>
                    <?php endif; ?>
                    <input type="hidden" name="old_image" value="<?= htmlspecialchars($product['image']) ?>">
                </div>

                <div class="col-md-6">
                    <label class="form-label">อัปโหลดรูปใหม่ (jpg, png)</label>
                    <input type="file" name="product_image" class="form-control">
                    <div class="form-check mt-2">
                        <input class="form-check-input" type="checkbox" name="remove_image" id="remove_image" value="1">
                        <label class="form-check-label" for="remove_image">ลบรูปเดิม</label>
                    </div>
                </div>

                <div class="col-12 text-center">
                    <button type="submit" class="btn btn-success">บันทึกการแก้ไข</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>