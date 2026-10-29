<?php
require_once(__DIR__ . '/../config.php');
require_once('CoaConnection.php');

class portalAuth extends DBConnection {

    public function __construct() {
        parent::__construct();
    }
    public function registrar() {
        $email = trim(strtolower($_POST['institutional_email'] ?? ''));
        $password = $_POST['password'] ?? '';
        $fullname = trim($_POST['fullname'] ?? '');

        if (empty($email) || empty($password) || empty($fullname)) {
            return json_encode(['status' => 'failed', 'msg' => 'Todos los campos son obligatorios.']);
        }
        if (strlen($password) < 6) {
            return json_encode(['status' => 'failed', 'msg' => 'La contraseña debe tener al menos 6 caracteres.']);
        }

        $coa = new CoaConnection();
        $validacion = $coa->validar($email);
        if (!$validacion['valido']) {
            return json_encode(['status' => 'failed', 'msg' => $validacion['mensaje']]);
        }

        $email = $this->conn->real_escape_string($email);
        $chk = $this->conn->query("SELECT id FROM portal_users WHERE institutional_email = '{$email}'");
        if ($chk && $chk->num_rows > 0) {
            return json_encode(['status' => 'failed', 'msg' => 'Ya existe una cuenta registrada con ese correo.']);
        }

        $role = $validacion['rol'];
        $nombre_final = $this->conn->real_escape_string($fullname);
        $coa_ref = $validacion['coa_ref_id'] ? "'" . $this->conn->real_escape_string($validacion['coa_ref_id']) . "'" : "NULL";
        $hash = password_hash($password, PASSWORD_DEFAULT);

        $sql = "INSERT INTO portal_users (fullname, institutional_email, password, role, coa_ref_id) 
                VALUES ('{$nombre_final}', '{$email}', '{$hash}', '{$role}', {$coa_ref})";
        $save = $this->conn->query($sql);

        if ($save) {
            return json_encode(['status' => 'success', 'msg' => 'Cuenta creada exitosamente. Ahora puedes iniciar sesión.']);
        } else {
            return json_encode(['status' => 'failed', 'msg' => 'Ocurrió un error al crear la cuenta. ' . $this->conn->error]);
        }
    }

    public function login() {
        $email = trim(strtolower($_POST['institutional_email'] ?? ''));
        $password = $_POST['password'] ?? '';

        $email_esc = $this->conn->real_escape_string($email);
        $qry = $this->conn->query("SELECT * FROM portal_users WHERE institutional_email = '{$email_esc}' AND status = 1 LIMIT 1");

        if ($qry && $qry->num_rows > 0) {
            $user = $qry->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                $_SESSION['portal_userdata'] = [
                    'id' => $user['id'],
                    'email' => $user['institutional_email'],
                    'fullname' => $user['fullname'],
                    'role' => $user['role']
                ];
                return json_encode(['status' => 'success']);
            }
        }
        return json_encode(['status' => 'failed', 'msg' => 'Correo o contraseña incorrectos.']);
    }

    public function logout() {
        unset($_SESSION['portal_userdata']);
        return json_encode(['status' => 'success']);
    }
    public function solicitar_reset() {
    $email = trim(strtolower($_POST['institutional_email'] ?? ''));
    $email_esc = $this->conn->real_escape_string($email);

    $qry = $this->conn->query("SELECT * FROM portal_users WHERE institutional_email = '{$email_esc}' LIMIT 1");
    if ($qry && $qry->num_rows > 0) {
        $user = $qry->fetch_assoc();
        $token = bin2hex(random_bytes(32));
        $expira = date('Y-m-d H:i:s', strtotime('+1 hour'));

        $this->conn->query("UPDATE portal_users SET reset_token = '{$token}', reset_expires = '{$expira}' WHERE id = {$user['id']}");

        require_once('Mailer.php');
        $link = base_url . 'Interfaz_users/reset_password.php?token=' . $token;
        Mailer::enviar_link_reset($user['institutional_email'], $user['fullname'], $link);
    }

    // Siempre responde "success", exista o no el correo (para no revelar qué correos están registrados)
    return json_encode(['status' => 'success']);
}

public function restablecer_password() {
    $token = $_POST['token'] ?? '';
    $password = $_POST['password'] ?? '';

    if (strlen($password) < 6) {
        return json_encode(['status' => 'failed', 'msg' => 'La contraseña debe tener al menos 6 caracteres.']);
    }

    $token_esc = $this->conn->real_escape_string($token);
    $qry = $this->conn->query("SELECT * FROM portal_users WHERE reset_token = '{$token_esc}' AND reset_expires > NOW() LIMIT 1");

    if (!$qry || $qry->num_rows == 0) {
        return json_encode(['status' => 'failed', 'msg' => 'El enlace es inválido o ya expiró. Solicita uno nuevo.']);
    }

    $user = $qry->fetch_assoc();
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $this->conn->query("UPDATE portal_users SET password = '{$hash}', reset_token = NULL, reset_expires = NULL WHERE id = {$user['id']}");

    return json_encode(['status' => 'success']);
}
}

ob_start(); // capturamos cualquier salida accidental (warnings, notices, espacios)

$action = !isset($_GET['f']) ? 'none' : strtolower($_GET['f']);
$auth = new portalAuth();
$respuesta = '';
switch ($action) {
    case 'registrar':
        $respuesta = $auth->registrar();
        break;
    case 'login':
        $respuesta = $auth->login();
        break;
    case 'logout':
        $respuesta = $auth->logout();
        break;
        case 'solicitar_reset':
            $respuesta = $auth->solicitar_reset();
            break;
            case 'restablecer_password':
                $respuesta = $auth->restablecer_password();
                break;
    default:
        $respuesta = json_encode(['status' => 'failed', 'msg' => 'Accion no valida']);
        break;
}

ob_end_clean(); // descartamos cualquier texto/warning acumulado antes del JSON
header('Content-Type: application/json');
echo $respuesta;
?>
