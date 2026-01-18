<?php
session_start();

require __DIR__ . '/../../Models/Promotion.php';
require __DIR__ . '/../../Models/Airline.php';
require __DIR__ . '/../../../db_connect.php';

if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
    header('Location: /admin/dashboard.php');
    exit;
}

$promotions = Promotion::allWithAirlines($conn);
$full_promotions = $promotions; // nếu cần nhân bản

$airlines = Airline::all($conn);
$promoList = Promotion::all($conn);

$conn->close();
