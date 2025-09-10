<?php
session_start();
require_once '../config.php';
require_once 'auth_admin.php';
// ลบสมำชกิ
if (isset($_GET['delete'])) {
$user_id = $_GET['delete'];
// ป้องกันลบตัวเอง
    if ($user_id != $_SESSION['user_id']) {
            $stmt = $conn->prepare("DELETE FROM users WHERE user_id = ? AND role = 'member'");
            $stmt->execute([$user_id]);
        }
        header("Location: users.php");
    exit;
}
// ดงึขอ้ มลู สมำชกิ
$stmt = $conn->prepare("SELECT * FROM users WHERE role = 'member' ORDER BY created_at DESC");
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<title>จัดการจัดการสมาชิก</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<style>
    /* ====== พื้นหลังและโครงสร้าง ====== */
body {
    background: linear-gradient(135deg, #f1f8e9 0%, #dcedc8 100%);
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    min-height: 100vh;
}

/* ====== หัวข้อ ====== */
h2 {
    color: #2e7d32;
    font-weight: 600;
    margin-bottom: 20px;
}

/* ====== ปุ่ม ====== */
.btn {
    border-radius: 8px !important;
    font-weight: 500;
    transition: transform 0.2s ease, background-color 0.2s ease;
}
.btn:hover {
    transform: translateY(-2px);
}

/* ปุ่มกลับ */
.btn-secondary {
    background-color: #81c784 !important;
    border: none !important;
    color: white !important;
}
.btn-secondary:hover {
    background-color: #66bb6a !important;
}

/* ปุ่มแก้ไข */
.btn-warning {
    background-color: #ffb74d !important;
    border: none !important;
    color: #4e342e !important;
}
.btn-warning:hover {
    background-color: #f57c00 !important;
    color: white !important;
}

/* ปุ่มลบ */
.btn-danger {
    background-color: #e57373 !important;
    border: none !important;
}
.btn-danger:hover {
    background-color: #c62828 !important;
}

/* ====== ตารางสมาชิก ====== */
.table {
    background: white;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 4px 10px rgba(0,0,0,0.08);
}
.table thead {
    background-color: #388e3c;
    color: white;
    text-align: center;
}
.table th, .table td {
    vertical-align: middle !important;
    text-align: center;
}
.table tbody tr:hover {
    background-color: #f1f8e9;
}

</style>
<body class="container mt-4">
<h2>จัดการสมาชิก</h2>
<a href="index.php" class="btn btn-secondary mb-3">← กลับหน้าผู้ดูแล</a>
<?php if (count($users) === 0): ?>
<div class="alert alert-warning">ยังไมมีสมาชิกในระบบ</div>
<?php else: ?>
<table class="table table-bordered">
<thead>
<tr>
<th>ชื่อผู้ใช้</th> ้
<th>ชื่อ-นามสกุล</th>
<th>อีเมล</th>
<th>วันที่สมัคร</th>
<th>จัดกำร</th>
</tr>
</thead>
<tbody>
<?php foreach ($users as $user): ?>
<tr>
<td><?= htmlspecialchars($user['username']) ?></td>
<td><?= htmlspecialchars($user['full_name']) ?></td>
<td><?= htmlspecialchars($user['email']) ?></td>
<td><?= $user['created_at'] ?></td>
<td>
<a href="edit_user.php?id=<?= $user['user_id'] ?>" class="btn btn-sm btn-warning">แก ้ไข
</a>
<a href="users.php?delete=<?= $user['user_id'] ?>" class="btn btn-sm btn-danger"
onclick="return confirm('คุณต้องการลบสมาชิกนี้หรือไม่?')">ลบ</a>
</td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
<?php endif; ?>
</body>
</html>