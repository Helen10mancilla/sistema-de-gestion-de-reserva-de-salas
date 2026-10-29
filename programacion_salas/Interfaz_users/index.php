<?php
require_once('config.php');
require_once('inc/sess_auth.php');
$user = $_SESSION['portal_userdata'];

$halls = $conn->query("SELECT * FROM assembly_hall WHERE status = 1 ORDER BY room_name ASC");

$sched_qry = $conn->query("SELECT s.*, a.room_name, pu.role FROM schedule_list s 
    INNER JOIN assembly_hall a ON a.id = s.assembly_hall_id 
    LEFT JOIN portal_users pu ON pu.id = s.portal_user_id");
$sched_data = [];
while ($row = $sched_qry->fetch_assoc()) { $sched_data[] = $row; }
$sched_json = json_encode($sched_data);

$mis_qry = $conn->query("SELECT s.*, a.room_name FROM schedule_list s INNER JOIN assembly_hall a ON a.id = s.assembly_hall_id WHERE s.portal_user_id = " . (int)$user['id'] . " ORDER BY s.datetime_start DESC");
?>
<!DOCTYPE html>
<html lang="es">
    <div id="detalle_modal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,.5); z-index:9999;">
    <div style="background:#fff; max-width:420px; margin:80px auto; border-radius:8px; padding:20px; position:relative;">
        <button onclick="$('#detalle_modal').hide();" style="position:absolute; top:10px; right:14px; border:none; background:none; font-size:20px; cursor:pointer;">&times;</button>
        <h5 class="mb-3"><b>Detalle de la reserva</b></h5>
        <div id="detalle_contenido"></div>
    </div>
</div>
<?php require_once('inc/header.php') ?>
<body>
    <div class="portal-navbar">
        <div>
            <img src="<?php echo validate_image($_settings->info('logo')) ?>" style="height: 32px; width: 32px; object-fit: contain; border-radius: 50%; background: #fff; padding: 2px; margin-right: 8px; vertical-align: middle;">
            <b><?php echo htmlspecialchars($_settings->info('short_name') ?: 'Portal de Reservas') ?></b> - <?php echo htmlspecialchars($user['fullname']) ?> (<?php echo $user['role'] == 'student' ? 'Estudiante' : 'Docente' ?>)
        </div>
        <div>
    <a href="#" id="ayuda_btn" class="mr-3"><i class="fas fa-question-circle"></i> Ayuda</a>
    <a href="#" id="logout_btn"><i class="fas fa-sign-out-alt"></i> Cerrar sesión</a>
</div>
    </div>
    <?php if ($_settings->info('anuncio')): ?>
        <div class="alert alert-warning text-center mb-0 rounded-0 py-2">
            <i class="fas fa-bullhorn mr-1"></i> <?php echo htmlspecialchars($_settings->info('anuncio')) ?>
        </div>
        <?php endif; ?>

    <div class="portal-wrap">
        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header"><b>Calendario de Salas</b></div>
                    <div class="card-body">
                        <div id="calendar"></div>
                    </div>
                </div>
                <div class="card mt-3 border-primary">
                    <div class="card-header bg-primary text-white"><b><i class="fas fa-calendar-check mr-1"></i>Consultar disponibilidad por fecha</b>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-2">Elige un día para ver las salas que estan disponibles y sus horarios </p>
                    <div class="input-group mb-3" style="max-width: 320px;"></div>
                    <div class="input-group-prepend">
                        <span class="input-group-text bg-primary text-white"><i class="fas fa-calendar-alt"></i></span>
                    </div>
                    <input type="date" id="fecha_disponibilidad" class="form-control form-control-lg">
                </div>
                <div class="mb-3">
                    <button type="button" class="btn btn-sm btn-outline-primary btn-fecha-rapida" data-dias="0">Hoy</button>
                    <button type="button" class="btn btn-sm btn-outline-primary btn-fecha-rapida" data-dias="1">Mañana</button>
                     <button type="button" class="btn btn-sm btn-outline-primary btn-fecha-rapida" data-dias="7">En una semana</button>
                </div>
                <div id="resultado_disponibilidad"></div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header"><b>Nueva Reserva</b></div>
                    <div class="card-body">
                        <form id="add_sched"> 
                            <div class="alert alert-info py-2 mb-3">
                                <i class="fas fa-info-circle mr-1"></i>
                                <b>Reglas de reserva:</b> máximo <b>2 horas</b> por reserva, dentro del horario
                                <b><?php echo $_settings->info('hora_apertura') ?: '08:00' ?> - <?php echo $_settings->info('hora_cierre') ?: '21:00' ?></b>,
                                de lunes a viernes.
                            </div>
                            <input type="hidden" name="id" id="sched_id" value="">
                            <div class="form-group">
                                <label>Sala</label>
                                <select name="assembly_hall_id" id="assembly_hall_id" class="form-control select2" required>
                                    <option value=""></option>
                                    <?php while ($row = $halls->fetch_assoc()): ?>
                                         <option value="<?php echo $row['id'] ?>" data-desc="<?php echo htmlspecialchars($row['description'] ?? '') ?>"><?php echo htmlspecialchars($row['room_name']) ?><?php echo $row['capacity'] ? ' (Capacidad: '.$row['capacity'].' personas)' : '' ?></option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                           <div class="form-group">
    <label>Desde</label>
    <div class="input-group date" id="dt_start_picker" data-target-input="nearest">
        <input type="text" class="form-control datetimepicker-input" data-target="#dt_start_picker" name="datetime_start" required autocomplete="off">
        <div class="input-group-append" data-target="#dt_start_picker" data-toggle="datetimepicker">
            <div class="input-group-text"><i class="fa fa-calendar"></i></div>
        </div>
    </div>
</div>
<div class="form-group">
    <label>Hasta</label>
    <div class="input-group date" id="dt_end_picker" data-target-input="nearest">
        <input type="text" class="form-control datetimepicker-input" data-target="#dt_end_picker" name="datetime_end" required autocomplete="off">
        <div class="input-group-append" data-target="#dt_end_picker" data-toggle="datetimepicker">
            <div class="input-group-text"><i class="fa fa-calendar"></i></div>
        </div>
    </div>
</div>
<div class="form-group">
    <label>Cédula</label>
    <input type="text" class="form-control" name="cedula" required>
</div>
<div class="form-group">
    <label>Cantidad de personas</label>
    <input type="number" min="1" class="form-control" name="cantidad_personas" id="cantidad_personas" required>

</div>
                            <div class="form-group">
                                <label>Observaciones</label>
                                <textarea rows="3" class="form-control" name="schedule_remarks"></textarea>
                            </div>
                            <div class="err-box"></div>
                            <button class="btn btn-primary btn-block">Reservar</button>
                        </form>
                    </div>
                </div>

                <div class="card mt-3">
                    <div class="card-header"><b>Mis Reservas</b></div>
                    <div class="card-body p-0">
                        <table class="table table-sm mb-0">
                            <tbody id="mis_reservas_body">
                                <?php if ($mis_qry->num_rows == 0): ?>
                                    <tr><td class="text-muted p-3">Aun no tienes reservas.</td></tr>
                                <?php endif; ?>
                                <?php while ($row = $mis_qry->fetch_assoc()): ?>
                                    <tr id="row_<?php echo $row['id'] ?>">
                                        <td class="p-2">
                                            <b><?php echo htmlspecialchars($row['room_name']) ?></b><br>
                                            <small><?php echo date('d/m/Y H:i', strtotime($row['datetime_start'])) ?> - <?php echo date('H:i', strtotime($row['datetime_end'])) ?></small>
                                        </td>
                                       <td class="p-2 text-right">
    <?php $vencida = strtotime($row['datetime_end']) + (24 * 3600) < time(); ?>
    <?php if ($vencida): ?>
        <span class="badge badge-secondary">Finalizada</span>
        <?php else: ?>
            <button class="btn-btnsm brn-outline-primary btn-editar mr-1"
            data-id="<?php echo $row['id'] ?>"
        data-hall="<?php echo $row['assembly_hall_id'] ?>"
        data-start="<?php echo date('Y-m-d\TH:i', strtotime($row['datetime_start'])) ?>"
        data-end="<?php echo date('Y-m-d\TH:i', strtotime($row['datetime_end'])) ?>"
         data-remarks="<?php echo htmlspecialchars($row['schedule_remarks'] ?? '') ?>"
        data-cantidad="<?php echo $row['cantidad_personas'] ?? '' ?>">Editar</button>
         <button class="btn btn-sm btn-outline-danger btn-cancel" data-id="<?php echo $row['id'] ?>">Cancelar</button>
         <?php endif; ?>
</td>
                                            
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    

    <?php require_once('inc/footer.php') ?>
    <script>
    var scheds = <?php echo $sched_json ?>;

    $('.select2').select2({ placeholder: "Selecciona una sala" });
    $('#dt_start_picker').datetimepicker({ format: 'YYYY-MM-DD HH:mm', icons: { time: 'far fa-clock' } });
    $('#dt_end_picker').datetimepicker({ format: 'YYYY-MM-DD HH:mm', icons: { time: 'far fa-clock' } });
    $('#dt_start_picker').on('change.datetimepicker', function(e){
        $('#dt_end_picker').datetimepicker('minDate', e.date);
    });

    var Calendar = FullCalendar.Calendar;
    var calendarEl = document.getElementById('calendar');
    var calendar = new Calendar(calendarEl, {
        headerToolbar: { left: 'prev,next today', center: 'title', right: 'dayGridMonth,timeGridWeek,timeGridDay' },
        themeSystem: 'bootstrap',
       events: function(info, successCallback) {
    var events = [];
    scheds.forEach(function(s){
        events.push({
            title: s.room_name + ' — ' + s.reserved_by + ' (' + (s.portal_user_id ? (s.role == 'student' ? 'Estudiante' : 'Docente') : 'Personal Administrativo') + ')',
            start: moment(s.datetime_start).format("YYYY-MM-DD HH:mm"),
            end: moment(s.datetime_end).format("YYYY-MM-DD HH:mm"),
            backgroundColor: 'var(--success)',
            borderColor: 'var(--primary)',
            extendedProps: {
                room_name: s.room_name,
                reserved_by: s.reserved_by,
                role: s.role,
                portal_user_id: s.portal_user_id,
                cedula: s.cedula,
                cantidad_personas: s.cantidad_personas,
                remarks: s.schedule_remarks
            }
        });
    });
    successCallback(events);
},
eventClick: function(info){
    var p = info.event.extendedProps;
    var tipo = p.portal_user_id ? (p.role == 'student' ? 'Estudiante' : 'Docente') : 'Personal Administrativo';
    var html = ''
        + '<p><b>Sala:</b> ' + p.room_name + '</p>'
        + '<p><b>Reservado por:</b> ' + p.reserved_by + '</p>'
        + '<p><b>Tipo de usuario:</b> ' + tipo + '</p>'
        + '<p><b>Cédula:</b> ' + (p.cedula ? p.cedula : '—') + '</p>'
        + '<p><b>Cantidad de personas:</b> ' + (p.cantidad_personas ? p.cantidad_personas : '—') + '</p>'
        + '<p><b>Desde:</b> ' + moment(info.event.start).format('DD/MM/YYYY HH:mm') + '</p>'
        + '<p><b>Hasta:</b> ' + moment(info.event.end).format('DD/MM/YYYY HH:mm') + '</p>'
        + '<p><b>Observaciones:</b><br>' + (p.remarks ? p.remarks : '—') + '</p>';
    $('#detalle_contenido').html(html);
    $('#detalle_modal').show();
},
editable: false,
selectable: false,
    });
    calendar.render();

    var salasDisponibles = <?php
    $halls2 = $conn->query("SELECT id, room_name FROM assembly_hall WHERE status = 1 ORDER BY room_name ASC");
    $halls_arr = [];
    while ($h = $halls2->fetch_assoc()) { $halls_arr[] = $h; }
    echo json_encode($halls_arr);
?>;

var HORA_APERTURA = <?php echo (int)explode(':', $_settings->info('hora_apertura') ?: '08:00')[0] ?>;  
var HORA_CIERRE = <?php echo (int)explode(':', $_settings->info('hora_cierre') ?: '21:00')[0] ?>; 

function esDiaLaboral(fecha) {
    var dia = moment(fecha, 'YYYY-MM-DD').day(); // 0=domingo ... 6=sabado
    return dia >= 1 && dia <= 5;
}

function mostrarDisponibilidad(fecha) {
    if (!fecha) { $('#resultado_disponibilidad').html(''); return; }

    if (!esDiaLaboral(fecha)) {
        $('#resultado_disponibilidad').html('<div class="alert alert-secondary mb-0">La biblioteca no abre ese día (atiende de lunes a viernes, 8:00 am - 9:00 pm).</div>');
        return;
    }

    var apertura = moment(fecha + ' ' + HORA_APERTURA + ':00', 'YYYY-MM-DD H:mm');
    var cierre = moment(fecha + ' ' + HORA_CIERRE + ':00', 'YYYY-MM-DD H:mm');

    var html = '<table class="table table-sm"><thead><tr><th>Sala</th><th>Disponibilidad </th></tr></thead><tbody>';

    salasDisponibles.forEach(function(sala){
        var ocupados = scheds.filter(function(s){
            return s.assembly_hall_id == sala.id && moment(s.datetime_start).format('YYYY-MM-DD') == fecha;
        }).sort(function(a,b){ return new Date(a.datetime_start) - new Date(b.datetime_start); });

        var badges = [];
        var cursor = apertura.clone();

        ocupados.forEach(function(o){
            var ini = moment(o.datetime_start);
            var fin = moment(o.datetime_end);
            if (ini.isAfter(cursor)) {
                badges.push('<span class="badge badge-success mr-1 mb-1">Libre ' + cursor.format('HH:mm') + '-' + ini.format('HH:mm') + '</span>');
            }
            badges.push('<span class="badge badge-danger mr-1 mb-1">Ocupada ' + ini.format('HH:mm') + '-' + fin.format('HH:mm') + '</span>');
            if (fin.isAfter(cursor)) cursor = fin.clone();
        });

        if (cursor.isBefore(cierre)) {
            badges.push('<span class="badge badge-success mr-1 mb-1">Libre ' + cursor.format('HH:mm') + '-' + cierre.format('HH:mm') + '</span>');
        }

        var hayLibre = badges.some(function(b){ return b.indexOf('badge-success') !== -1; });
        var detalle = hayLibre ? badges.join(' ') : '<span class="badge badge-dark">Totalmente ocupada, sin horarios libres</span>';

        html += '<tr><td>' + sala.room_name + '</td><td>' + detalle + '</td></tr>';
    });

    html += '</tbody></table>';
    $('#resultado_disponibilidad').html(html);
}
$('.btn-fecha-rapida').click(function(){
    var dias = parseInt($(this).data('dias'));
    var fecha = moment().add(dias, 'days').format('YYYY-MM-DD');
    $('#fecha_disponibilidad').val(fecha);
    mostrarDisponibilidad(fecha);
});

$('#fecha_disponibilidad').change(function(){
    mostrarDisponibilidad($(this).val());
});

$('#fecha_disponibilidad').val(moment().format('YYYY-MM-DD'));
mostrarDisponibilidad(moment().format('YYYY-MM-DD'));
  $(document).on('click', '.btn-editar', function(){
    $('#sched_id').val($(this).data('id'));
    $('#assembly_hall_id').val($(this).data('hall')).trigger('change');
    $('input[name="datetime_start"]').val($(this).data('start'));
    $('input[name="datetime_end"]').val($(this).data('end'));
    $('textarea[name="schedule_remarks"]').val($(this).data('remarks'));
    $('#cantidad_personas').val($(this).data('cantidad'));
    $('html, body').animate({ scrollTop: $('#add_sched').offset().top - 100 }, 300);
});


    $('#add_sched').submit(function(e){
        e.preventDefault();
        $('.err-box').html('');
        start_loader();
        $.ajax({
            url: _base_url_ + 'Interfaz_users/clases/portalMaster.php?f=' + ($('#sched_id').val() ? 'update_schedule' : 'save_schedule'),
            method: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(resp){
                end_loader();
                if(resp.status == 'success'){
                    alert_toast('Reserva creada. Te llegara un correo de confirmacion.','success');
                    setTimeout(()=> location.reload(), 1000);
                } else {
                    $('.err-box').html('<div class="alert alert-danger py-2">'+resp.err_msg+'</div>');
                }
            },
            error: function(){
                end_loader();
                alert_toast('Ocurrio un error.','error');
            }
        });
    });

    $(document).on('click', '.btn-cancel', function(){
        var id = $(this).data('id');
        if(!confirm('¿Cancelar esta reserva?')) return;
        start_loader();
        $.ajax({
            url: _base_url_ + 'Interfaz_users/clases/portalMaster.php?f=cancel_schedule',
            method: 'POST',
            data: { id: id },
            dataType: 'json',
            success: function(resp){
                end_loader();
                if(resp.status == 'success'){
                    alert_toast('Reserva cancelada. Te llegara un correo de confirmacion.','success');
                    setTimeout(()=> location.reload(), 1000);
                } else {
                    alert_toast(resp.msg,'error');
                }
            },
            error: function(){
                end_loader();
                alert_toast('Ocurrio un error.','error');
            }
        });
    });

    $('#logout_btn').click(function(e){
        e.preventDefault();
        $.get(_base_url_ + 'Interfaz_users/clases/portalAuth.php?f=logout', function(){
            location.href = _base_url_ + 'Interfaz_users/login.php';
        });
    });
        $('#ayuda_btn').click(function(e){
    e.preventDefault();
    $('#detalle_contenido').html(
        '<p>Si necesitas ayuda, escríbenos a:</p>'
        + '<div class="input-group mb-2">'
        + '  <input type="text" class="form-control" id="correo_ayuda" value="biblioteca@admon.uniajc.edu.co" readonly>'
        + '  <div class="input-group-append">'
        + '    <button class="btn btn-primary" type="button" id="copiar_correo"><i class="fas fa-copy"></i> Copiar</button>'
        + '  </div>'
        + '</div>'
        + '<small class="text-muted">Pégalo en tu correo (Outlook) y escríbenos tu consulta.</small>'
    );
    $('#detalle_modal').show();
});

$(document).on('click', '#copiar_correo', function(){
    var campo = document.getElementById('correo_ayuda');
    campo.select();
    document.execCommand('copy');
    alert_toast('Correo copiado', 'success');
});
    </script>
</body>
</html>