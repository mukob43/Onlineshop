<?php

require '../config.php';
require 'auth_admin.php'; // ตรวจสอบสทิ ธิ์admin

// เพิ่มหมวดหมู่
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_category'])) {
$category_name = trim($_POST['category_name']);
if ($category_name) {
$stmt = $conn->prepare("INSERT INTO categories(category_name) VALUES (?)");
$stmt->execute([$category_name]);
header("Location: category.php");
exit;
}
}
// ลบหมวดหมู่ (แบบไมม่ กี ำรตรวจสอบวำ่ ยังมสี นิ คำ้ในหมวดหมนู่ หี้ รอื ไม)่
// if (isset($_GET['delete'])) {
// $category_id = $_GET['delete'];
// $stmt = $conn->prepare("DELETE FROM categories WHERE category_id = ?");
// $stmt->execute([$category_id]);
// header("Location: category.php");
// exit;
// }
// ลบหมวดหมู่
// ตรวจสอบวำ่ หมวดหมนู่ ี้ยังถกู ใชอ้ยหู่ รอื ไม่
if (isset($_GET['delete'])) {
$category_id = $_GET['delete'];
// ตรวจสอบวำ่ หมวดหมนู่ ยี้ ังถูกใชอ้ยหู่ รอื ไม่
$stmt = $conn->prepare("SELECT COUNT(*) FROM products WHERE category_id = ?");
$stmt->execute([$category_id]);
$productCount = $stmt->fetchColumn();
if ($productCount > 0) {
// ถำ้มสี นิ คำ้อยใู่ นหมวดหมนู่ ี้
$_SESSION['error'] = "ไม่สามารถลบหมวดหมู่นี้ได้ เนื่องจากยังมีสินค้าใช้งานหมวดหมู่นี้อยู่";
} else {
// ถำ้ไมม่ สี นิ คำ้ ใหล้ บได ้
$stmt = $conn->prepare("DELETE FROM categories WHERE category_id = ?");
$stmt->execute([$category_id]);
$_SESSION['success'] = "ลบหมวดหมู่เรียบร้อยแล้ว";
}
header("Location: category.php");
exit;
}

// แก ้ไขหมวดหมู่
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_category'])) {
$category_id = $_POST['category_id'];
$category_name = trim($_POST['new_name']);
if ($category_name) {
$stmt = $conn->prepare("UPDATE categories SET category_name = ? WHERE category_id =?");

$stmt->execute([$category_name, $category_id]);
header("Location: category.php");
exit;
}
}
// ดึงหมวดหมู่ทั้งหมด
$categories = $conn->query("SELECT * FROM categories ORDER BY category_id ASC")->fetchAll(PDO::FETCH_ASSOC);
// โคด้ นเี้ขยีนตอ่ กันยำวบรรทัดเดยี วไดเ้พรำะ ผลลัพธจ์ ำกเมธอดหนงึ่ สำมำรถสง่ ตอ่ (chaining) ให้เมธอดถัดไปทันที โดยไม่ต ้อง
// $pdo->query("...")->fetchAll(...);
// หำกเขียนแยกเป็นหลำยบรรทัดจะเป็นแบบนี้:
// $stmt = $pdo->query("SELECT * FROM categories ORDER BY category_id ASC");
// $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ควรเขยีนแยกบรรทัดเมอื่ จะ ใช ้$stmt ซ ้ำหลำยครัง้ (เชน่ fetch ทีละ row, ตรวจจ ำนวนแถว)

// หรือเขียนแบบ prepare , execute
// $stmt = $pdo->prepare("SELECT * FROM categories ORDER BY category_id ASC");
// $stmt->execute();
// $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<title>จัดกำรหมวดหมู่</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
/* Custom CSS for light green theme - minimal additions */
.bg-light-green { background: linear-gradient(135deg, #f0f9f0 0%, #e8f5e8 100%) !important; }
.bg-green-gradient { background: linear-gradient(135deg, #4caf50 0%, #388e3c 100%) !important; }
.bg-light-green-card { background: rgba(255, 255, 255, 0.95) !important; }
.text-green-dark { color: #2e7d32 !important; }
.text-green { color: #388e3c !important; }
.shadow-green { box-shadow: 0 20px 40px rgba(46, 125, 50, 0.1), 0 10px 20px rgba(46, 125, 50, 0.05) !important; }
.btn-green { background: linear-gradient(135deg, #66bb6a 0%, #4caf50 100%) !important; border: none !important; }
.btn-green-primary { background: linear-gradient(135deg, #4caf50 0%, #2e7d32 100%) !important; border: none !important; }
.border-green { border: 2px solid #c8e6c9 !important; }
.bg-green-light-stripe:nth-child(even) { background: rgba(232, 245, 233, 0.3) !important; }
.hover-green:hover { background: rgba(165, 214, 167, 0.4) !important; transform: scale(1.01) !important; }
</style>
</head>
<body class="bg-light-green min-vh-100">
<div class="container mt-4 mb-4 p-4 bg-light-green-card rounded-4 shadow-green">
<h2 class="text-green-dark fw-bold text-center mb-4 display-6">จัดการหมวดหมู่สินค้า</h2>

<?php if (isset($_SESSION['error'])): ?>
<div class="alert alert-danger rounded-3 border-0 shadow-sm"><?= $_SESSION['error'] ?></div>
<?php unset($_SESSION['error']); ?>
<?php endif; ?>

<?php if (isset($_SESSION['success'])): ?>
<div class="alert alert-success rounded-3 border-0 shadow-sm bg-success bg-opacity-10 text-success border border-success border-opacity-25"><?= $_SESSION['success'] ?></div>
<?php unset($_SESSION['success']); ?>
<?php endif; ?>

<a href="index.php" class="btn btn-green text-white mb-3 rounded-pill px-4 py-2 text-decoration-none shadow-sm">
    <i class="me-2">←</i>กลับหน้าผู้ดูแล
</a>

<div class="row g-3 mb-4 p-3 bg-success bg-opacity-10 rounded-3 border border-success border-opacity-25 border-2" style="border-style: dashed !important;">
    <form method="post" class="row g-3 w-100">
        <div class="col-md-6">
            <input type="text" name="category_name" class="form-control rounded-3 border-green py-2 px-3" placeholder="ชื่อหมวดหมู่" required>
        </div>
        <div class="col-md-2">
            <button type="submit" name="add_category" class="btn btn-green-primary text-white rounded-pill px-4 py-2 fw-semibold shadow-sm">เพิ่มหมวดหมู่</button>
        </div>
    </form>
</div>

<h5 class="text-green fw-semibold mb-3 fs-4">รายการหมวดหมู่</h5>

<div class="table-responsive">
    <table class="table table-bordered-0 rounded-3 overflow-hidden shadow-sm bg-white">
        <thead class="bg-green-gradient text-white">
            <tr>
                <th class="py-3 px-4 text-center fw-semibold">ชื่อหมวดหมู่</th> 
                <th class="py-3 px-4 text-center fw-semibold">แก้ไขชื่อ</th>
                <th class="py-3 px-4 text-center fw-semibold">จัดการ</th>
            </tr>
        </thead>
        <tbody>
        <?php $row_count = 0; ?>
        <?php foreach ($categories as $cat): ?>
            <tr class="<?= ($row_count % 2 == 0) ? 'bg-success bg-opacity-5' : '' ?> hover-green transition-all">
                <td class="py-3 px-4 text-green-dark fw-medium"><?= htmlspecialchars($cat['category_name']) ?></td>
                <td class="py-3 px-4">
                    <form method="post" class="d-flex gap-2 align-items-center">
                        <input type="hidden" name="category_id" value="<?= $cat['category_id'] ?>">
                        <input type="text" name="new_name" class="form-control form-control-sm rounded-3 border-green flex-grow-1" placeholder="ชื่อใหม่" required>
                        <button type="submit" name="update_category" class="btn btn-warning btn-sm rounded-pill px-3 text-dark fw-medium shadow-sm">แก้ไข</button>
                    </form>
                </td>
                <td class="py-3 px-4 text-center">
                    <a href="category.php?delete=<?= $cat['category_id'] ?>" 
                       class="btn btn-danger btn-sm rounded-pill px-3 shadow-sm text-white" 
                       onclick="return confirm('คุณต้องการลบหมวดหมู่นี้หรือไม่?')">ลบ</a>
                </td>
            </tr>
            <?php $row_count++; ?>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

</div>

<style>
/* Additional hover effects and transitions */
.transition-all { transition: all 0.3s ease !important; }
.btn:hover { transform: translateY(-2px) !important; }
.form-control:focus { border-color: #4caf50 !important; box-shadow: 0 0 0 0.25rem rgba(76, 175, 80, 0.25) !important; }
.table tbody tr:hover { transform: scale(1.005) !important; box-shadow: 0 4px 12px rgba(76, 175, 80, 0.15) !important; }

/* Responsive adjustments */
@media (max-width: 768px) {
    .container { margin: 1rem !important; padding: 1.5rem !important; }
    .display-6 { font-size: 1.75rem !important; }
    .btn { padding: 0.5rem 1rem !important; font-size: 0.9rem !important; }
}
</style>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>