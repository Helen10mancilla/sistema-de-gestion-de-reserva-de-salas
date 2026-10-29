<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['portal_userdata'])) {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'failed', 'msg' => 'Sesion expirada. Vuelve a iniciar sesion.']);
    exit;
}
?>