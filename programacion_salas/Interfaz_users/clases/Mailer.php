<?php
require_once(__DIR__ . '/../lib/Exception.php');
require_once(__DIR__ . '/../lib/PHPMailer.php');
require_once(__DIR__ . '/../lib/SMTP.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Mailer {

    public static function notificar_reserva($destinatario_email, $destinatario_nombre, $tipo, $datos) {
        if (empty(SMTP_USER) || empty(SMTP_PASS)) {
            error_log("Mailer: SMTP no configurado todavia. No se envio correo a {$destinatario_email}.");
            return false;
        }

        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = SMTP_HOST;
            $mail->SMTPAuth = true;
            $mail->Username = SMTP_USER;
            $mail->Password = SMTP_PASS;
            $mail->SMTPSecure = 'tls';
            $mail->Port = SMTP_PORT;
            $mail->CharSet = 'UTF-8';

            $mail->setFrom(SMTP_USER, SMTP_FROM_NAME);
            $mail->addAddress($destinatario_email, $destinatario_nombre);

            $titulos = [
                'creada' => 'Tu reserva fue confirmada',
                'cancelada' => 'Tu reserva fue cancelada',
                'modificada' => 'Tu reserva fue modificada',
            ];
            $mail->Subject = $titulos[$tipo] ?? 'Actualizacion de tu reserva';

            $mail->isHTML(true);
            $mail->Body = "<p>Hola <b>{$destinatario_nombre}</b>,</p>
                <p>Tu reserva de la sala <b>{$datos['room_name']}</b> ha sido <b>{$tipo}</b>.</p>
                <p><b>Desde:</b> {$datos['datetime_start']}<br>
                <b>Hasta:</b> {$datos['datetime_end']}</p>
                
                <p>Si no reconoces este cambio, contacta al administrador del sistema.</p>";

            $mail->send();
            return true;
        } catch (Exception $e) {
            error_log("Mailer error: " . $mail->ErrorInfo);
            return false;
        }
    }
    public static function enviar_link_reset($destinatario_email, $destinatario_nombre, $link) {
    if (empty(SMTP_USER) || empty(SMTP_PASS)) {
        error_log("Mailer: SMTP no configurado. No se envio el link de reset a {$destinatario_email}.");
        return false;
    }

    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = SMTP_HOST;
        $mail->SMTPAuth = true;
        $mail->Username = SMTP_USER;
        $mail->Password = SMTP_PASS;
        $mail->SMTPSecure = 'tls';
        $mail->Port = SMTP_PORT;
        $mail->CharSet = 'UTF-8';

        $mail->setFrom(SMTP_USER, SMTP_FROM_NAME);
        $mail->addAddress($destinatario_email, $destinatario_nombre);
        $mail->Subject = 'Restablecer tu contraseña';
        $mail->isHTML(true);
        $mail->Body = "<p>Hola <b>{$destinatario_nombre}</b>,</p>
            <p>Haz clic en el siguiente enlace para restablecer tu contraseña (válido por 1 hora):</p>
            <p><a href='{$link}'>{$link}</a></p>
            <p>Si no solicitaste esto, ignora este correo.</p>";

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Mailer error: " . $mail->ErrorInfo);
        return false;
    }
}
}

?>
