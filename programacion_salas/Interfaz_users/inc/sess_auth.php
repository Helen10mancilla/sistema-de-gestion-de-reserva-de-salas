<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
$current = $_SERVER['REQUEST_URI'];

if (!isset($_SESSION['portal_userdata']) && !strpos($current, 'login.php') && !strpos($current, 'register.php')) {
    header('Location: ' . base_url . 'Interfaz_users/login.php');
    exit;
}
if (isset($_SESSION['portal_userdata']) && (strpos($current, 'login.php') || strpos($current, 'register.php'))) {
    header('Location: ' . base_url . 'Interfaz_users/index.php');
    exit;
}
?>