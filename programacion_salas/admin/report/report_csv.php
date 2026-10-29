<?php
require_once('../../config.php');
require_once('../inc/sess_auth.php');

$dstart = isset($_GET['dstart']) ? $_GET['dstart'] : date("Y-m-d", strtotime(date("Y-m-d")." -1 week"));
$dend = isset($_GET['dend']) ? $_GET['dend'] : date("Y-m-d");
$rid = isset($_GET['aid']) ? (int)$_GET['aid'] : 0;

$where = "";
if ($rid > 0) {
    $where = " and a.id = '{$rid}'";
}

$qry = $conn->query("SELECT s.*, a.room_name, a.description FROM `schedule_list` s
    INNER JOIN `assembly_hall` a ON a.id = s.assembly_hall_id
    WHERE ((date(datetime_start) BETWEEN '{$dstart}' and '{$dend}') OR (date(datetime_end) BETWEEN '{$dstart}' and '{$dend}')) {$where}
    ORDER BY unix_timestamp(datetime_start) asc, unix_timestamp(datetime_end) asc");

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="reporte_reservas_' . $dstart . '_a_' . $dend . '.csv"');

$output = fopen('php://output', 'w');
fputcsv($output, ['#', 'Fecha Inicio', 'Fecha Fin', 'Sala', 'Descripcion', 'Reservado por', 'Observaciones']);

$i = 1;
while ($row = $qry->fetch_assoc()) {
    fputcsv($output, [
        $i++,
        date("d/m/Y h:i A", strtotime($row['datetime_start'])),
        date("d/m/Y h:i A", strtotime($row['datetime_end'])),
        $row['room_name'],
        $row['description'],
        ucwords($row['reserved_by']),
        $row['schedule_remarks'],
    ]);
}
fclose($output);
exit;
?>