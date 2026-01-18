<?php
$servername = "localhost";
$username = "root";
$password = "48194007"; 
$port = 3306; 
$dbname = "flynow_db"; 

$conn = new mysqli($servername, $username, $password, $dbname, $port); 

if ($conn->connect_error) {
    die("Kết nối database thất bại: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");

?>