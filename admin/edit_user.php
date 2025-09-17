<?php

require '../config.php';
require 'auth_admin.php'; 


if (!isset($_GET['id'])) {
header("Location: users.php");
exit;
}
// TODO-4: ดึงค่ำ id และ "แคสต์เป็น int" เพื่อควำมปลอดภัย
$user_id = (int)$_GET['id'];
// ดงึขอ้ มลู สมำชกิทจี่ ะถกู แกไ้ข
/*
TODO-5: เตรียม/รัน SELECT (เฉพำะ role = 'member')
SQL แนะน ำ:
SELECT * FROM users WHERE user_id = ? AND role = 'member'
- ใช ้prepare + execute([$user_id])
- fetch(PDO::FETCH_ASSOC) แล้วเก็บใน $user
*/
$stmt = $conn->prepare("SELECT * FROM users WHERE user_id = ? AND role = 'member'");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
// TODO-6: ถ ้ำไม่พบข ้อมูล -> แสดงข ้อควำมและ exit;
if (!$user) {
echo "<h3>ไม่พบสมาชิก</h3>";
exit;
}
// ========== เมอื่ ผใู้ชก้ด Submit ฟอร์ม ==========
$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
// TODO-7: รับค่ำ POST + trim
$username   = trim($_POST['username']);
$full_name  = trim($_POST['full_name']);
$email      = trim($_POST['email']);

$password = $_POST['password'];
$confirm = $_POST['confirm_password'];

// TODO-8: ตรวจควำมครบถ ้วน และตรวจรูปแบบ email
if ($username === '' || $email === '') {
$error = "กรุณำกรอกข ้อมูลให้ครบถ ้วน";
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
$error = "รูปแบบอีเมลไม่ถูกต ้อง";
}
// TODO-9: ถ ้ำ validate ผ่ำน ใหต้ รวจสอบซ ้ำ (username/email ชนกับคนอนื่ ทไี่ มใ่ ชต่ ัวเองหรือไม่)
// SQL แนะน ำ:
// SELECT 1 FROM users WHERE (username = ? OR email = ?) AND user_id != ?
if (!$error) {
$chk = $conn->prepare("SELECT 1 FROM users WHERE (username = ? OR email = ?) AND user_id != ?");
$chk->execute([$username, $email, $user_id]);
if ($chk->fetch()) {
$error = "ชื่อผู้ใช้หรืออีเมลนี้มีอยู่ในระบบ";
}

// ตรวจรหัสผ่ำน (กรณีต้องกำรเปลี่ยน)
// เงื่อนไข: อนุญำตให้ปล่อยว่ำงได ้ (คือไม่เปลี่ยนรหัสผ่ำน)
$updatePassword = false;
$hashed = null;

if (!$error && ($password !== '' || $confirm !== '')) {
// TODO: นศ.เตมิกตกิ ำ เชน่ ยำว >= 6 และรหัสผ่ำนตรงกัน
if (strlen($password) < 6) {
$error = "รหัสผ่านต้องยาวอย่างน้อย 6อักขระ";
} elseif ($password !== $confirm) {
$error = "รหัสผ่านใหม่กับยืนยันรหัสผ่านไม่ตรงกัน";
} else {
// แฮชรหัสผ่ำน
$hashed = password_hash($password, PASSWORD_DEFAULT);
$updatePassword = true;
}
}
// สร ้ำง SQL UPDATE แบบยืดหยุ่น (ถ ้ำไม่เปลี่ยนรหัสผ่ำนจะไม่แตะ field password)
if (!$error) {
if ($updatePassword) {
// อัปเดตรวมรหัสผ่ำน
$sql = "UPDATE users
SET username = ?, full_name = ?, email = ?, password = ?
WHERE user_id = ?";
$args = [$username, $full_name, $email, $hashed, $user_id];
} else {
// อัปเดตเฉพำะข ้อมูลทั่วไป
$sql = "UPDATE users
SET username = ?, full_name = ?, email = ?
WHERE user_id = ?";
$args = [$username, $full_name, $email, $user_id];
}
$upd = $conn->prepare($sql);
$upd->execute($args);
header("Location: users.php");
exit;
}
// เขียน update แบบปกต:ิ ถำ้ไมซ่ ้ำ -> ท ำ UPDATE
// if (!$error) {
// $upd = $pdo->prepare("UPDATE users SET username = ?, full_name = ?, email = ? WHERE user_id = ?");
// $upd->execute([$username, $full_name, $email, $user_id]);
// // TODO-11: redirect กลับหน้ำ users.php หลังอัปเดตส ำเร็จ
// header("Location: users.php");
// ex

}
// TODO-10: ถำ้ไมซ่ ้ำ -> ท ำ UPDATE
// SQL แนะน ำ:
// UPDATE users SET username = ?, full_name = ?, email = ? WHERE user_id = ?


// OPTIONAL: อัปเดตค่ำ $user เพอื่ สะทอ้ นคำ่ ทชี่ อ่ งฟอรม์ (หำกมีerror)
$user['username'] = $username;
$user['full_name'] = $full_name;
$user['email'] = $email;
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<title>แก้ไขสมาชิก</title>
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
.form-section { background: rgba(232, 245, 233, 0.2) !important; }
</style>
</head>
<body class="bg-light-green min-vh-100">
<div class="container mt-4 mb-4 p-4 bg-light-green-card rounded-4 shadow-green">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h2 class="text-green-dark fw-bold text-center mb-4 display-6">
                <i class="me-2">👤</i>แก้ไขข้อมูลสมาชิก
            </h2>
            
            <div class="mb-4">
                <a href="users.php" class="btn btn-green text-white rounded-pill px-4 py-2 text-decoration-none shadow-sm">
                    <i class="me-2">←</i>กลับหน้ารายชื่อสมาชิก
                </a>
            </div>
            
            <?php if (isset($error)): ?>
            <div class="alert alert-danger rounded-3 border-0 shadow-sm mb-4">
                <i class="me-2">⚠️</i><?= htmlspecialchars($error) ?>
            </div>
            <?php endif; ?>
            
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-header bg-green-gradient text-white py-3">
                    <h5 class="card-title mb-0 fw-semibold text-center">
                        <i class="me-2">📝</i>ข้อมูลส่วนตัว
                    </h5>
                </div>
                <div class="card-body p-4">
                    <form method="post" class="row g-4">
                        <!-- ข้อมูลพื้นฐาน -->
                        <div class="col-12">
                            <div class="form-section p-3 rounded-3 border border-success border-opacity-25">
                                <h6 class="text-green fw-semibold mb-3">
                                    <i class="me-2">ℹ️</i>ข้อมูลพื้นฐาน
                                </h6>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label text-green-dark fw-medium">
                                            <i class="me-1">👤</i>ชื่อผู้ใช้
                                        </label>
                                        <input type="text" 
                                               name="username" 
                                               class="form-control rounded-3 border-green py-2 px-3" 
                                               required 
                                               value="<?= htmlspecialchars($user['username']) ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label text-green-dark fw-medium">
                                            <i class="me-1">📝</i>ชื่อ - นามสกุล
                                        </label>
                                        <input type="text" 
                                               name="full_name" 
                                               class="form-control rounded-3 border-green py-2 px-3" 
                                               value="<?= htmlspecialchars($user['full_name']) ?>">
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label text-green-dark fw-medium">
                                            <i class="me-1">📧</i>อีเมล
                                        </label>
                                        <input type="email" 
                                               name="email" 
                                               class="form-control rounded-3 border-green py-2 px-3" 
                                               required 
                                               value="<?= htmlspecialchars($user['email']) ?>">
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- เปลี่ยนรหัสผ่าน -->
                        <div class="col-12">
                            <div class="form-section p-3 rounded-3 border border-warning border-opacity-25 bg-warning bg-opacity-5">
                                <h6 class="text-warning-emphasis fw-semibold mb-3">
                                    <i class="me-2">🔐</i>เปลี่ยนรหัสผ่าน
                                    <small class="text-muted fw-normal">(เว้นว่างหากไม่ต้องการเปลี่ยน)</small>
                                </h6>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label text-green-dark fw-medium">
                                            <i class="me-1">🔒</i>รหัสผ่านใหม่
                                        </label>
                                        <input type="password" 
                                               name="password" 
                                               class="form-control rounded-3 border-green py-2 px-3" 
                                               placeholder="ใส่รหัสผ่านใหม่">
                                        <div class="form-text text-muted">
                                            <i class="me-1">💡</i>รหัสผ่านต้องมีอย่างน้อย 6 ตัวอักษร
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label text-green-dark fw-medium">
                                            <i class="me-1">🔒</i>ยืนยันรหัสผ่านใหม่
                                        </label>
                                        <input type="password" 
                                               name="confirm_password" 
                                               class="form-control rounded-3 border-green py-2 px-3" 
                                               placeholder="ยืนยันรหัสผ่าน">
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- ปุ่มบันทึก -->
                        <div class="col-12 text-center mt-4">
                            <button type="submit" class="btn btn-green-primary text-white rounded-pill px-5 py-3 fw-semibold shadow-sm btn-lg">
                                <i class="me-2">💾</i>บันทึกการแก้ไข
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- ข้อมูลเพิ่มเติม -->
            <div class="mt-4 p-3 bg-info bg-opacity-10 rounded-3 border border-info border-opacity-25">
                <div class="d-flex align-items-center text-info-emphasis">
                    <i class="me-2">💡</i>
                    <div>
                        <small class="fw-semibold">คำแนะนำ:</small>
                        <small class="d-block">• ตรวจสอบข้อมูลให้ถูกต้องก่อนบันทึก</small>
                        <small class="d-block">• หากไม่ต้องการเปลี่ยนรหัสผ่าน ให้เว้นช่องรหัสผ่านว่างไว้</small>
                        <small class="d-block">• รหัสผ่านใหม่ต้องมีความยาวอย่างน้อย 6 ตัวอักษร</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Additional hover effects and transitions */
.transition-all { transition: all 0.3s ease !important; }
.btn:hover { transform: translateY(-2px) !important; }
.form-control:focus { 
    border-color: #4caf50 !important; 
    box-shadow: 0 0 0 0.25rem rgba(76, 175, 80, 0.25) !important; 
    transform: scale(1.02) !important;
}
.card { transition: all 0.3s ease !important; }
.card:hover { transform: translateY(-5px) !important; }

/* Form enhancements */
.form-control { transition: all 0.3s ease !important; }
.form-control::placeholder { color: #81c784 !important; opacity: 0.8 !important; }

/* Icon styles */
i { font-style: normal !important; }

/* Responsive adjustments */
@media (max-width: 768px) {
    .container { margin: 1rem !important; padding: 1.5rem !important; }
    .display-6 { font-size: 1.75rem !important; }
    .btn-lg { padding: 0.75rem 2rem !important; font-size: 1rem !important; }
    .card-body { padding: 2rem !important; }
}

/* Custom focus styles */
*:focus {
    outline: 2px solid #4caf50 !important;
    outline-offset: 2px !important;
}
</style>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>