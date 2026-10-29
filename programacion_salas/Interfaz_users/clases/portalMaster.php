<?php
require_once(__DIR__ . '/../config.php');
require_once(__DIR__ . '/../inc/sess_auth_ajax.php');
require_once('Mailer.php');

class PortalMaster extends DBConnection {
    private $user;

    public function __construct() {
        parent::__construct();
        $this->user = $_SESSION['portal_userdata'];
    }


    private function get_room_name($assembly_hall_id) {
        $id = (int)$assembly_hall_id;
        $r = $this->conn->query("SELECT room_name FROM assembly_hall WHERE id = {$id}");
        return $r && $r->num_rows > 0 ? $r->fetch_assoc()['room_name'] : 'Sala';
    }

    public function save_schedule() {
        $assembly_hall_id = (int)($_POST['assembly_hall_id'] ?? 0);
        $datetime_start = $this->conn->real_escape_string($_POST['datetime_start'] ?? '');
        $datetime_end = $this->conn->real_escape_string($_POST['datetime_end'] ?? '');
        $schedule_remarks = $this->conn->real_escape_string($_POST['schedule_remarks'] ?? '');
        $cedula = $this->conn->real_escape_string($_POST['cedula'] ?? '');
        $reserved_by = $this->conn->real_escape_string($this->user['fullname']);
        $cantidad_personas = (int) ($_POST['cantidad_personas']?? 0);
        $portal_user_id = (int)$this->user['id'];

        if (empty($assembly_hall_id) || empty($datetime_start) || empty($datetime_end)) {
            return json_encode(['status' => 'failed', 'err_msg' => 'Completa todos los campos.']);
        }
        if (strtotime($datetime_end) < strtotime($datetime_start)) {
            return json_encode(['status' => 'failed', 'err_msg' => 'La fecha/hora es invalida.']);
        }

        $d_start = strtotime($datetime_start);
        $d_end = strtotime($datetime_end);
        $chk_result = $this->conn->query("SELECT * FROM schedule_list WHERE assembly_hall_id = {$assembly_hall_id} AND datetime_start < '{$datetime_end}' AND datetime_end > '{$datetime_start}'");
        $chk = $chk_result->num_rows;

        if ($chk > 0) {
            return json_encode(['status' => 'failed', 'err_msg' => 'Esa sala ya tiene una reserva en ese horario.']);
        }

        $sql = "INSERT INTO schedule_list (assembly_hall_id, reserved_by, datetime_start, datetime_end, schedule_remarks, portal_user_id, cedula, cantidad_personas)
                VALUES ({$assembly_hall_id}, '{$reserved_by}', '{$datetime_start}', '{$datetime_end}', '{$schedule_remarks}', {$portal_user_id}, '{$cedula}', {$cantidad_personas})";
        $save = $this->conn->query($sql);

        if ($save) {
            Mailer::notificar_reserva($this->user['email'], $this->user['fullname'], 'creada', [
                'room_name' => $this->get_room_name($assembly_hall_id),
                'datetime_start' => $datetime_start,
                'datetime_end' => $datetime_end,
            ]);
            return json_encode(['status' => 'success']);
        }
        return json_encode(['status' => 'failed', 'err_msg' => 'Error al guardar: ' . $this->conn->error]);
    }
    public function update_schedule() {
    $id = (int)($_POST['id'] ?? 0);
    $portal_user_id = (int)$this->user['id'];
    $assembly_hall_id = (int)($_POST['assembly_hall_id'] ?? 0);
    $datetime_start = $this->conn->real_escape_string($_POST['datetime_start'] ?? '');
    $datetime_end = $this->conn->real_escape_string($_POST['datetime_end'] ?? '');
    $schedule_remarks = $this->conn->real_escape_string($_POST['schedule_remarks'] ?? '');
    $cantidad_personas = (int) ($_POST['cantidad_personas'] ?? 0);

    $own = $this->conn->query("SELECT * FROM schedule_list WHERE id = {$id} AND portal_user_id = {$portal_user_id}");
    if (!$own || $own->num_rows == 0) {
        return json_encode(['status' => 'failed', 'err_msg' => 'No puedes modificar esta reserva.']);
    }

    if (empty($assembly_hall_id) || empty($datetime_start) || empty($datetime_end)) {
        return json_encode(['status' => 'failed', 'err_msg' => 'Completa todos los campos.']);
    }
    if (strtotime($datetime_end) < strtotime($datetime_start)) {
        return json_encode(['status' => 'failed', 'err_msg' => 'La fecha/hora es invalida.']);
    }

    $d_start = strtotime($datetime_start);
    $d_end = strtotime($datetime_end);
    $chk_result = $this->conn->query("SELECT * FROM schedule_list WHERE assembly_hall_id = {$assembly_hall_id} AND id != {$id} AND datetime_start < '{$datetime_end}' AND datetime_end > '{$datetime_start}'");
    if ($chk_result->num_rows > 0) {
        return json_encode(['status' => 'failed', 'err_msg' => 'Esa sala ya tiene una reserva en ese horario.']);
    }

    $sql = "UPDATE schedule_list SET assembly_hall_id = {$assembly_hall_id}, datetime_start = '{$datetime_start}',
            datetime_end = '{$datetime_end}', schedule_remarks = '{$schedule_remarks}', cantidad_personas ='{$cantidad_personas}' WHERE id = {$id}";
    $save = $this->conn->query($sql);

    if ($save) {
        Mailer::notificar_reserva($this->user['email'], $this->user['fullname'], 'modificada', [
            'room_name' => $this->get_room_name($assembly_hall_id),
            'datetime_start' => $datetime_start,
            'datetime_end' => $datetime_end,
        ]);
        return json_encode(['status' => 'success']);
    }
    return json_encode(['status' => 'failed', 'err_msg' => 'Error al actualizar: ' . $this->conn->error]);
}

    public function cancel_schedule() {
        $id = (int)($_POST['id'] ?? 0);
        $portal_user_id = (int)$this->user['id'];

        $r = $this->conn->query("SELECT s.*, a.room_name FROM schedule_list s
                                  INNER JOIN assembly_hall a ON a.id = s.assembly_hall_id
                                  WHERE s.id = {$id} AND s.portal_user_id = {$portal_user_id}");
        if (!$r || $r->num_rows == 0) {
            return json_encode(['status' => 'failed', 'msg' => 'No puedes cancelar esta reserva.']);
        }
        $row = $r->fetch_assoc();

        $del = $this->conn->query("DELETE FROM schedule_list WHERE id = {$id}");
        if ($del) {
            Mailer::notificar_reserva($this->user['email'], $this->user['fullname'], 'cancelada', $row);
            return json_encode(['status' => 'success']);
        }
        return json_encode(['status' => 'failed', 'msg' => 'Error al cancelar.']);
    }
}

ob_start(); // capturamos cualquier salida accidental (warnings, notices, espacios)

$action = !isset($_GET['f']) ? 'none' : strtolower($_GET['f']);
$Master = new PortalMaster();
$respuesta = '';
switch ($action) {
    case 'save_schedule':
        $respuesta = $Master->save_schedule();
        break;
    case 'cancel_schedule':
        $respuesta = $Master->cancel_schedule();
        break;
    case 'update_schedule':
    $respuesta = $Master->update_schedule();
       break;
    default:
        $respuesta = json_encode(['status' => 'failed', 'msg' => 'Accion no valida']);
        break;
}

ob_end_clean(); // descartamos cualquier texto/warning acumulado antes del JSON
header('Content-Type: application/json');
echo $respuesta;
?>