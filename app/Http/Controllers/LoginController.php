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

        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if ($email === '' || $password === '') {
            return "Vui lòng nhập đầy đủ email và mật khẩu";
        }

        // 🔹 lấy user theo email
        $stmt = $this->conn->prepare(
            "SELECT id, fullname, password, role FROM users WHERE email = ? LIMIT 1"
        );
        $stmt->bind_param("s", $email);
        $stmt->execute();

        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if (!$user) {
            return "Email hoặc mật khẩu không đúng";
        }

        // 🔹 kiểm tra mật khẩu
        if (!password_verify($password, $user['password'])) {
            return "Email hoặc mật khẩu không đúng";
        }

        // ✅ LOGIN THÀNH CÔNG
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['fullname'] = $user['fullname'];
        $_SESSION['role'] = $user['role'];

        // 🔹 phân quyền
        if ($user['role'] === 'admin') {
            header("Location: /admin.php");
        } else {
            header("Location: /");
        }
        exit;
    }
}
