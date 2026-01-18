<?php
include __DIR__.'/../../Models/User.php';

$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullname = trim($_POST['fullname']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (User::checkEmail($conn, $email)) {
        $msg = "Email này đã được đăng ký.";
    } else {
        if (User::register($conn, $fullname, $email, $password)) {
            $msg = "Đăng ký thành công! Hãy đăng nhập.";
        } else {
            $msg = "Có lỗi xảy ra, vui lòng thử lại.";
        }
    }
}
