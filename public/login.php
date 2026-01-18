<?php
session_start();
require_once __DIR__.'/../app/Http/Controllers/LoginController.php';
include __DIR__.'/includes/header.php';

include __DIR__.'/../db_connect.php';

$controller = new LoginController($conn);
$err = $controller->handleLogin();
?>

<!doctype html>
<html lang="vi">
<head>
  <meta charset="utf-8">
  <title>Đăng nhập Flynow</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="./css/login.css">
</head>

<body>
  <video autoplay muted loop class="video-bg">
    <source src="./img/clouds2.mp4" type="video/mp4">
  </video>

  <div class="login-container">
    <div class="login-card">
      <h4>Đăng nhập Flynow</h4>
      <?php if($err): ?><div class="alert alert-danger"><?=$err?></div><?php endif; ?>
      <form method="post">
        <div class="mb-3">
          <label>Email</label>
          <input type="email" class="form-control" name="email" placeholder="Nhập email..." required>
        </div>
        <div class="mb-3">
          <label>Mật khẩu</label>
          <input type="password" class="form-control" name="password" placeholder="••••••" required>
        </div>
        <button class="btn btn-primary w-100 mb-3">Đăng nhập</button>
        <div class="text-center">
          <a href="register.php" class="btn-link">Chưa có tài khoản? Đăng ký ngay</a>
        </div>
      </form>
    </div>
  </div>
</body>
</html>

<?php include __DIR__.'/includes/footer.php'; ?>
