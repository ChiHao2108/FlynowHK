<?php
require_once __DIR__.'/../../Models/User.php';

class LoginController {
    private $conn;
    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function handleLogin() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return null;
        }

        // kiểm tra login
        if ($loginSuccess) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['fullname'] = $user['fullname'];

            header("Location: /admin.php");
            exit; // 🚨 BẮT BUỘC
        }

        return "Email hoặc mật khẩu không đúng";
    }
}
