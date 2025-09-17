<?php

require_once '../config.php';
require_once 'auth_admin.php';
// ลบสมาชิก
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
// ดึงข้อมูลสมาชิก
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body style="background: linear-gradient(135deg, #f1f8e9 0%, #dcedc8 100%); font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; min-height: 100vh;">
    <div class="container mt-4" style="background: rgba(255, 255, 255, 0.9); border-radius: 15px; box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1); padding: 2rem;">
        
        <h2 class="text-center mb-4" style="color: #2e7d32; font-weight: 600;">
            👥 จัดการสมาชิก
        </h2>
        
        <a href="index.php" class="btn mb-3" 
           style="background-color: #81c784; border: none; color: white; border-radius: 8px; font-weight: 500; transition: transform 0.2s ease, background-color 0.2s ease;"
           onmouseover="this.style.backgroundColor='#66bb6a'; this.style.transform='translateY(-2px)'"
           onmouseout="this.style.backgroundColor='#81c784'; this.style.transform='translateY(0)'">
            ← กลับหน้าผู้ดูแล
        </a>
        
        <?php if (count($users) === 0): ?>
            <div class="alert alert-warning" style="border-radius: 10px; background: #fff3cd; border: 1px solid #ffeaa7; color: #856404;">
                ⚠️ ยังไม่มีสมาชิกในระบบ
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-bordered" 
                       style="background: white; border-radius: 10px; overflow: hidden; box-shadow: 0 4px 10px rgba(0,0,0,0.08);">
                    <thead style="background-color: #388e3c; color: white;">
                        <tr class="text-center">
                            <th style="font-weight: 600; vertical-align: middle;">ชื่อผู้ใช้</th>
                            <th style="font-weight: 600; vertical-align: middle;">ชื่อ-นามสกุล</th>
                            <th style="font-weight: 600; vertical-align: middle;">อีเมล</th>
                            <th style="font-weight: 600; vertical-align: middle;">วันที่สมัคร</th>
                            <th style="font-weight: 600; vertical-align: middle;">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                            <tr style="vertical-align: middle;" 
                                onmouseover="this.style.backgroundColor='#f1f8e9'" 
                                onmouseout="this.style.backgroundColor='white'">
                                
                                <td class="text-center" style="font-weight: 500;">
                                    <?= htmlspecialchars($user['username']) ?>
                                </td>
                                
                                <td class="text-center" style="font-weight: 500;">
                                    <?= htmlspecialchars($user['full_name']) ?>
                                </td>
                                
                                <td class="text-center">
                                    <?= htmlspecialchars($user['email']) ?>
                                </td>
                                
                                <td class="text-center" style="font-weight: 500;">
                                    <?= $user['created_at'] ?>
                                </td>
                                
                                <td class="text-center">
                                    <a href="edit_user.php?id=<?= $user['user_id'] ?>" 
                                       class="btn btn-sm me-2" 
                                       style="background-color: #ffb74d; border: none; color: #4e342e; border-radius: 8px; font-weight: 500; transition: transform 0.2s ease, background-color 0.2s ease;"
                                       onmouseover="this.style.backgroundColor='#f57c00'; this.style.color='white'; this.style.transform='translateY(-2px)'"
                                       onmouseout="this.style.backgroundColor='#ffb74d'; this.style.color='#4e342e'; this.style.transform='translateY(0)'">
                                        ✏️ แก้ไข
                                    </a>
                                    
                                    <form action="deluser_Sweet.php" method="POST" style="display:inline;">
                                        <input type="hidden" name="u_id" value="<?php echo $user['user_id']; ?>">
                                        <button type="button" 
                                                class="delete-button btn btn-sm" 
                                                data-user-id="<?php echo $user['user_id']; ?>"
                                                style="background-color: #e57373; border: none; color: white; border-radius: 8px; font-weight: 500; transition: transform 0.2s ease, background-color 0.2s ease;"
                                                onmouseover="this.style.backgroundColor='#c62828'; this.style.transform='translateY(-2px)'"
                                                onmouseout="this.style.backgroundColor='#e57373'; this.style.transform='translateY(0)'">
                                            🗑️ ลบ
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

    <script>
        // ฟังก์ชันสำหรับแสดงกล่องยืนยัน SweetAlert2
        function showDeleteConfirmation(userId) {
            Swal.fire({
                title: 'คุณแน่ใจหรือไม่?',
                text: 'คุณจะไม่สามารถเรียกคืนข้อมูลกลับได้!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'ลบ',
                cancelButtonText: 'ยกเลิก',
                confirmButtonColor: '#c62828',
                cancelButtonColor: '#81c784',
                background: '#fff',
                customClass: {
                    popup: 'border-radius: 15px'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    // หากผู้ใช้ยืนยัน ให้ส่งค่าฟอร์มไปยัง delete.php เพื่อลบข้อมูล
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = 'deluser_Sweet.php';
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'u_id';
                    input.value = userId;
                    form.appendChild(input);
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }
        
        // แนบตัวตรวจจับเหตุการณ์คลิกกับปุ่มลบทั้งหมดที่มีคลาส delete-button
        const deleteButtons = document.querySelectorAll('.delete-button');
        deleteButtons.forEach((button) => {
            button.addEventListener('click', () => {
                const userId = button.getAttribute('data-user-id');
                showDeleteConfirmation(userId);
            });
        });
    </script>
</body>
</html>