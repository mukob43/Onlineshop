<?php
session_start();
require '../config.php';
require 'auth_admin.php'; // ตรวจสอบสิทธิ์ผู้ดูแลระบบ

?>
<!DOCTYPE html>
<html lang="th">
    <head>
        <meta charset="UTF-8">
        <title>แผงควบคุมผู้ดูแลระบบ</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    </head>
    <style>
        /* ====== พื้นหลังและฟอนต์หลัก ====== */
body {
    background: linear-gradient(135deg, #e8f5e9 0%, #c8e6c9 100%);
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    min-height: 100vh;
}

/* ====== หัวข้อ ====== */
h2 {
    color: #2e7d32;
    font-weight: 600;
}
p {
    color: #388e3c;
}

/* ====== ปุ่มหลัก ====== */
.btn {
    border-radius: 10px !important;
    font-weight: 500;
    transition: transform 0.2s ease, background-color 0.2s ease;
}
.btn:hover {
    transform: translateY(-3px);
}

/* ปุ่ม จัดการสมาชิก */
.btn-warning {
    background-color: #ffb74d !important;
    border: none !important;
    color: #4e342e !important;
}
.btn-warning:hover {
    background-color: #f57c00 !important;
    color: white !important;
}

/* ปุ่ม จัดการหมวดหมู่ */
.btn-dark {
    background-color: #2e7d32 !important;
    border: none !important;
}
.btn-dark:hover {
    background-color: #1b5e20 !important;
}

/* ปุ่ม จัดการสินค้า */
.btn-primary {
    background-color: #66bb6a !important;
    border: none !important;
}
.btn-primary:hover {
    background-color: #43a047 !important;
}

/* ปุ่ม จัดการคำสั่งซื้อ */
.btn-success {
    background-color: #4caf50 !important;
    border: none !important;
}
.btn-success:hover {
    background-color: #388e3c !important;
}

/* ปุ่ม ออกจากระบบ */
.btn-secondary {
    background-color: #81c784 !important;
    border: none !important;
}
.btn-secondary:hover {
    background-color: #66bb6a !important;
}

    </style>
    <body class="container mt-4">
        <h2>ระบบผู้ดูแลระบบ</h2>
        <p class="mb-4">ยินดีต้อนรับ, <?= htmlspecialchars($_SESSION['username']) ?></p>
        <div class="row">
            <div class="col-md-4 mb-3">
                <a href="users.php" class="btn btn-warning w-100">จัดการสมาชิก</a>
            </div>
            <div class="col-md-4 mb-3">
                <a href="categories.php" class="btn btn-dark w-100">จัดการหมวดหมู่</a>
            </div>
            <div class="col-md-4 mb-3">
                <a href="products.php" class="btn btn-primary w-100">จัดการสินค้า</a>
            </div>
            <div class="col-md-4 mb-3">
                <a href="orders.php" class="btn btn-success w-100">จัดกำรคำสั่งซื้อ </a>
            </div>
        </div>
        <a href="../logout.php" class="btn btn-secondary mt-3">ออกจำกระบบ</a>
    </body>
    </html>