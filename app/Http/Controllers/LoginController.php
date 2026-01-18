<?php
require_once __DIR__.'/../../Models/User.php';

class LoginController {
    private $conn;
    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function handleLogin() {
        $err = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email']);
            $pass  = trim($_POST['password']);

            $user = User::login($this->conn, $email, $pass);

            if ($user) {
                $_SESSION['user_id']  = $user['id'];
                $_SESSION['fullname'] = $user['fullname'];
                $_SESSION['role']     = $user['role'];
                $_SESSION['avatar']   = $user['avatar'] ?: 'img/default-avatar.png';

                if ($user['role'] === 'admin') {
                    header('Location: /admin/dashboard.php');
                } else {
                    header('Location: /index.php');
                }
                exit;
            } else {
                $err = "Sai email hoặc mật khẩu.";
            }
        }
        return $err;
    }
}
