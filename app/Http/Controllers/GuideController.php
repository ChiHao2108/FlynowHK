<?php
session_start();

class GuideController
{
    public static function redirectIfAdmin()
    {
        if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
            header('Location: /admin/dashboard.php');
            exit;
        }
    }
}