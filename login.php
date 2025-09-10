<?php
    session_start();
    require_once 'config.php';
    $error = '';
    if($_SERVER['REQUEST_METHOD'] == 'POST'){
        $usernameOrEmail = trim($_POST['username_or_email']);            
        $password = $_POST['password'];

        $sql = "SELECT * FROM users WHERE (username = ? OR email = ?)";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$usernameOrEmail, $usernameOrEmail]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if($user && password_verify($password, $user['password'])){
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            if($user['role'] === 'admin'){
                header("Location: admin/index.php");
            }else{
                header("Location: index.php");
            }
            exit();
        }else {
            $error = "ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง";
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background: linear-gradient(135deg, #c8facc, #a5f5a5, #81c784);
      background-size: 300% 300%;
      animation: gradientShift 10s ease infinite;
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    @keyframes gradientShift {
      0% { background-position: 0% 50%; }
      50% { background-position: 100% 50%; }
      100% { background-position: 0% 50%; }
    }

    .login-card {
      background: rgba(255, 255, 255, 0.95);
      backdrop-filter: blur(20px);
      border-radius: 20px;
      box-shadow: 0 15px 40px rgba(76, 175, 80, 0.25);
      padding: 3rem 2.5rem;
      width: 100%;
      max-width: 450px;
      border: 1px solid rgba(76, 175, 80, 0.15);
    }

    .login-header {
      text-align: center;
      margin-bottom: 2rem;
    }

    .login-title {
      font-size: 2.3rem;
      font-weight: 700;
      background: linear-gradient(135deg, #4caf50, #66bb6a);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      margin-bottom: 0.5rem;
    }

    .login-subtitle {
      color: #388e3c;
      font-weight: 400;
      font-size: 1.1rem;
    }

    .form-label {
      color: #4caf50;
      font-weight: 600;
      font-size: 0.95rem;
      margin-bottom: 0.5rem;
    }

    .form-control {
      border: 2px solid #c8e6c9;
      border-radius: 12px;
      padding: 0.9rem;
      font-size: 1rem;
      background: rgba(255, 255, 255, 0.95);
      transition: all 0.3s ease;
    }

    .form-control:focus {
      border-color: #81c784;
      box-shadow: 0 0 0 0.2rem rgba(129, 199, 132, 0.25);
      transform: translateY(-2px);
    }

    .btn-login {
      background: linear-gradient(135deg, #66bb6a, #4caf50);
      border: none;
      border-radius: 12px;
      padding: 0.9rem;
      font-weight: 600;
      font-size: 1.1rem;
      color: white;
      width: 100%;
      margin-top: 0.5rem;
      box-shadow: 0 8px 20px rgba(76, 175, 80, 0.3);
      transition: all 0.3s ease;
    }

    .btn-login:hover {
      background: linear-gradient(135deg, #4caf50, #81c784);
      transform: translateY(-3px);
      box-shadow: 0 12px 25px rgba(76, 175, 80, 0.4);
    }

    .btn-link {
      display: block;
      margin-top: 1rem;
      color: #4caf50;
      font-weight: 500;
      text-align: center;
      text-decoration: none;
      transition: all 0.3s ease;
    }

    .btn-link:hover {
      color: #388e3c;
      text-decoration: underline;
    }

    .alert {
      border-radius: 12px;
      border: none;
      font-weight: 500;
      padding: 1rem 1.2rem;
      margin-bottom: 1.5rem;
    }

    .alert-success {
      background: rgba(129, 199, 132, 0.1);
      color: #2e7d32;
      border-left: 4px solid #66bb6a;
    }

    .alert-danger {
      background: rgba(239, 68, 68, 0.1);
      color: #991b1b;
      border-left: 4px solid #ef4444;
    }
  </style>
</head>
<body>
  <div class="login-card">
    <div class="login-header">
      <h1 class="login-title">เข้าสู่ระบบ</h1>
      <p class="login-subtitle">ยินดีต้อนรับกลับ</p>
    </div>

    <?php if (isset($_GET['register']) && $_GET['register'] === 'success'): ?>
    <div class="alert alert-success">สมัครสมาชิกสำเร็จ กรุณาเข้าสู่ระบบ</div>
    <?php endif; ?>
    <?php if (!empty($error)): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="post" class="row g-3">
      <div class="col-12">
        <label for="username_or_email" class="form-label">ชื่อผู้ใช้หรืออีเมล</label>
        <input type="text" name="username_or_email" id="username_or_email" class="form-control" required>
      </div>
      <div class="col-12">
        <label for="password" class="form-label">รหัสผ่าน</label>
        <input type="password" name="password" id="password" class="form-control" required>
      </div>
      <div class="col-12">
        <button type="submit" class="btn-login">เข้าสู่ระบบ</button>
        <a href="register.php" class="btn-link">สมัครสมาชิก</a>
      </div>
    </form>
  </div>
</body>
</html>
