<?php
$servername = getenv("DB_HOST");
$username   = getenv("DB_USERNAME");
$password   = getenv("DB_PASSWORD");
$dbname     = getenv("DB_DATABASE");
$port       = getenv("DB_PORT");

$conn = new mysqli($servername, $username, $password, $dbname, $port);

if ($conn->connect_error) {
    die("Kết nối database thất bại: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");
