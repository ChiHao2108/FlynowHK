<?php
session_start();
require_once __DIR__.'/../../../db_connect.php'; 

$admin_email = 'admin@gmail.com';
$check = $conn->prepare("SELECT id FROM users WHERE email = ?");
$check->bind_param("s", $admin_email);
$check->execute();
$result = $check->get_result();

if ($result->num_rows === 0) {
    $fullname = "Quản trị viên";
    $password_plain = "admin123";
    $role = "admin";
    $password_hashed = password_hash($password_plain, PASSWORD_DEFAULT);

    $insert = $conn->prepare("INSERT INTO users (fullname, email, password, role) VALUES (?, ?, ?, ?)");
    $insert->bind_param("ssss", $fullname, $admin_email, $password_hashed, $role);
    $insert->execute();
}

if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
    header('Location: /admin/dashboard.php');
    exit;
}

$search_results = [];
$tickets = [];
$search_meta = null;
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['search_submit'])) {

    $from = trim($_POST['from'] ?? '');
    $to = trim($_POST['to'] ?? '');
    $date_go = trim($_POST['date_go'] ?? '');
    $date_return = trim($_POST['date_return'] ?? '');
    $trip_type = $_POST['trip_type'] ?? 'round';
    $adults = (int)($_POST['adults'] ?? 1);

    if ($trip_type === 'round' && $date_go && $date_return) {
        if (strtotime($date_return) < strtotime($date_go)) {
            $error_message = 'Ngày về không được cũ hơn Ngày đi. Vui lòng chọn lại.';
        }
    }

    $search_meta = [
        'from' => $from,
        'to' => $to,
        'date_go' => $date_go,
        'date_return' => $date_return,
        'trip_type' => $trip_type,
        'adults' => $adults
    ];

    if (!$error_message) {
        foreach ($tickets as $t) {
            $hay = strtolower($t['route'] . ' ' . $t['title'] . ' ' . $t['desc']);
            if ($from && $to) {
                if (strpos($hay, strtolower($from)) !== false &&
                    strpos($hay, strtolower($to)) !== false) {
                    $search_results[] = $t;
                }
            } else {
                $search_results[] = $t;
            }
        }
    }

    if (empty($search_results)) {
        $search_results = $tickets;
    }
}
