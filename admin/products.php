<?php 
require '../config.php'; 
require 'auth_admin.php';

// เพิ่มสินค้าใหม่
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_product'])) {
    $name = trim($_POST['product_name']);
    $description = trim($_POST['description']);
    $price = floatval($_POST['price']); // floatval() ใช้แปลงเป็น float
    $stock = intval($_POST['stock']); // intval() ใช้แปลงเป็น integer
    $category_id = intval($_POST['category_id']); // ค่าที่ได้จากฟอร์มเป็น string เสมอ

    if ($name && $price > 0) { // ตรวจสอบชื่อ และราคาสินค้า
        $stmt = $conn->prepare("INSERT INTO products(product_name,description,price,stock,category_id) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$name,$description,$price,$stock,$category_id]);
        header("Location: products.php");
        exit;
    }
    // ถ้าเขียนให้อ่านง่ายขึ้น สามารถเขียนแบบด้านล่าง
    // if (!empty($name) && $price > 0) {
    //     // ผ่านเงื่อนไข: มีชื่อสินค้า และ ราคามากกว่า 0
    // }
}

// ลบสินค้า
if (isset($_GET['delete'])) {
    $product_id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM products WHERE product_id = ?");
    $stmt->execute([$product_id]);
    header("Location: products.php");
    exit;
}

// ดึงรายการสินค้า
$stmt = $conn->query("SELECT p.*, c.category_name FROM products p LEFT JOIN categories c ON p.category_id = c.category_id ORDER BY p.created_at DESC");
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ดึงหมวดหมู่
$categories = $conn->query("SELECT * FROM categories")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>จัดการสินค้า</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

</head>
<body style="background: linear-gradient(135deg, #d4edda 0%, #f8f9fa 100%); min-height: 100vh;">
    <div class="container mt-4" style="background: rgba(255, 255, 255, 0.9); border-radius: 15px; box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1); padding: 2rem;">
        <h2 class="text-center mb-4" style="color: #155724; font-weight: 600;">
            จัดการสินค้า
        </h2>
        
        <a href="index.php" class="btn mb-3" style="background: #c3e6cb; color: #155724; border: 1px solid #28a745; border-radius: 8px;">
            ← กลับหน้าผู้ดูแล
        </a>

        <!-- ฟอร์มเพิ่มสินค้าใหม่ -->
        <div class="mb-4 p-4" style="background: #d4edda; border-radius: 10px; border: 1px solid #c3e6cb;">
            <form method="post" class="row g-3">
                <h5 class="mb-3" style="color: #155724; font-weight: 600;">✨ เพิ่มสินค้า</h5>
                
                <div class="col-md-4">
                    <input type="text" name="product_name" class="form-control" 
                           placeholder="ชื่อสินค้า" required 
                           style="border: 2px solid #c3e6cb; border-radius: 8px; font-weight: 500;">
                </div>
                
                <div class="col-md-2">
                    <input type="number" step="0.01" name="price" class="form-control" 
                           placeholder="ราคา" required 
                           style="border: 2px solid #c3e6cb; border-radius: 8px; font-weight: 500;">
                </div>
                
                <div class="col-md-2">
                    <input type="number" name="stock" class="form-control" 
                           placeholder="จำนวน" required 
                           style="border: 2px solid #c3e6cb; border-radius: 8px; font-weight: 500;">
                </div>
                
                <div class="col-md-2">
                    <select name="category_id" class="form-select" required 
                            style="border: 2px solid #c3e6cb; border-radius: 8px; font-weight: 500;">
                        <option value="">เลือกหมวดหมู่</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat['category_id'] ?>">
                                <?= htmlspecialchars($cat['category_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="col-12">
                    <textarea name="description" class="form-control" 
                              placeholder="รายละเอียดสินค้า" rows="2" 
                              style="border: 2px solid #c3e6cb; border-radius: 8px;"></textarea>
                </div>
                
                <div class="col-12">
                    <button type="submit" name="add_product" class="btn btn-success" 
                            style="background: linear-gradient(45deg, #28a745, #20c997); border: none; border-radius: 8px; font-weight: 500; padding: 10px 30px; font-size: 16px;">
                        ➕ เพิ่มสินค้า
                    </button>
                </div>
            </form>
        </div>

        <!-- แสดงรายการสินค้า , แก้ไข , ลบ -->
        <h5 class="mb-3" style="color: #155724; font-weight: 600;">📦 รายการสินค้า</h5>
        
        <div class="table-responsive">
            <table class="table table-bordered" style="background: white; border-radius: 10px; overflow: hidden; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);">
                <thead style="background: linear-gradient(45deg, #28a745, #20c997); color: white;">
                    <tr class="text-center">
                        <th style="font-weight: 600;">ชื่อสินค้า</th>
                        <th style="font-weight: 600;">หมวดหมู่</th>
                        <th style="font-weight: 600;">ราคา</th>
                        <th style="font-weight: 600;">คงเหลือ</th>
                        <th style="font-weight: 600;">จัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($products as $p): ?>
                        <tr style="vertical-align: middle;">
                            <td style="font-weight: 500;"><?= htmlspecialchars($p['product_name']) ?></td>
                            <td class="text-center"><?= htmlspecialchars($p['category_name']) ?></td>
                            <td class="text-end" style="font-weight: 600; color: #28a745;">
                                <?= number_format($p['price'], 2) ?> บาท
                            </td>
                            <td class="text-center" style="font-weight: 500;"><?= $p['stock'] ?></td>
                            <td class="text-center">
                                <a href="products.php?delete=<?= $p['product_id'] ?>" 
                                   class="btn btn-sm btn-danger me-2" 
                                   onclick="return confirm('ยืนยันการลบสินค้านี้?')"
                                   style="border-radius: 6px;">🗑️ ลบ</a>
                                <a href="edit_product.php?id=<?= $p['product_id'] ?>" 
                                   class="btn btn-sm btn-warning"
                                   style="border-radius: 6px;">✏️ แก้ไข</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>