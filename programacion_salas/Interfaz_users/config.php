<?php
require_once(__DIR__ . '/../config.php');

define('DOMINIO_ESTUDIANTES', $_ENV['DOMINIO ESTUDIANTES']);
define('DOMINIO_DOCENTES', $_ENV['DOMINIO DOCENTES']);

define('COA_MODO_PRUEBA', true); // Cambia a false cuando ya tengas acceso real a coa

define('COA_DB_HOST', 'localhost');
define('COA_DB_USER', 'root');
define('COA_DB_PASS', '');
define('COA_DB_NAME', 'coa');

define('SMTP_HOST', $_ENV['SMTP_HOST']);
define('SMTP_PORT', $_ENV['SMTP_PORT']);
define('SMTP_USER',$_ENV['SMTP_USER']);
define('SMTP_PASS', $_ENV['SMTP_PASS']);
define('SMTP_FROM_NAME', 'Sistema de Reservas de Salas');
?>