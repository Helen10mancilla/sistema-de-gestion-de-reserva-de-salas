<h1 class="">Bienvenid@ a tu <?php echo $_settings->info('name') ?></h1>
<hr>
<div class="row">
    <div class="col-lg-3 col-6">
    <div class="small-box bg-info">
        <div class="inner">
        <h3><?php echo $conn->query("SELECT * FROM `assembly_hall`")->num_rows; ?></h3>
        <p>Total de salas</p>
        </div>
        <div class="icon">
        <i class="fas fa-door-open"></i>
        </div>
        <a href="./?page=assembly_hall" class="small-box-footer">Más información <i class="fas fa-arrow-circle-right"></i></a>
    </div>
    </div>

    <div class="col-lg-3 col-6">
    <div class="small-box bg-success">
        <div class="inner">
        <h3><?php echo $conn->query("SELECT * FROM `schedule_list`")->num_rows; ?></h3>
        <p>Total de Reservas</p>
        </div>
        <div class="icon">
        <i class="fa fa-calendar-week"></i>
        </div>
        <a href="./?page=schedules" class="small-box-footer">Más Información <i class="fas fa-arrow-circle-right"></i></a>
    </div>
    </div>

    <?php
    $total_mes = $conn->query("SELECT * FROM `schedule_list` WHERE MONTH(datetime_start) = MONTH(CURDATE()) AND YEAR(datetime_start) = YEAR(CURDATE())")->num_rows;
    $top_sala_qry = $conn->query("SELECT a.room_name, COUNT(*) as total FROM schedule_list s INNER JOIN assembly_hall a ON a.id = s.assembly_hall_id GROUP BY s.assembly_hall_id ORDER BY total DESC LIMIT 1");
    $top_sala = $top_sala_qry->num_rows > 0 ? $top_sala_qry->fetch_assoc() : null;
    ?>

    <div class="col-lg-3 col-6">
    <div class="small-box bg-warning">
        <div class="inner">
        <h3><?php echo $total_mes; ?></h3>
        <p>Reservas este mes</p>
        </div>
        <div class="icon">
        <i class="fas fa-calendar-check"></i>
        </div>
        <a href="./?page=report" class="small-box-footer">Ver reporte <i class="fas fa-arrow-circle-right"></i></a>
    </div>
    </div>

   
</div>

<div class="row">
    <div class="col-lg-6">
        <div class="card card-outline card-primary">
            <div class="card-header"><h3 class="card-title">Salas más reservadas</h3></div>
            <div class="card-body">
                <canvas id="chart_salas" style="min-height:250px"></canvas>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card card-outline card-primary">
            <div class="card-header"><h3 class="card-title">Reservas por mes (últimos 6 meses)</h3></div>
            <div class="card-body">
                <canvas id="chart_meses" style="min-height:250px"></canvas>
            </div>
        </div>
    </div>
</div>

<?php
$salas_data = [];
$sr = $conn->query("SELECT a.room_name, COUNT(*) as total FROM schedule_list s INNER JOIN assembly_hall a ON a.id = s.assembly_hall_id GROUP BY s.assembly_hall_id ORDER BY total DESC LIMIT 8");
while ($row = $sr->fetch_assoc()) { $salas_data[] = $row; }

$meses_data = [];
$mr = $conn->query("SELECT DATE_FORMAT(datetime_start, '%Y-%m') as ym, COUNT(*) as total FROM schedule_list WHERE datetime_start >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH) GROUP BY ym ORDER BY ym ASC");
while ($row = $mr->fetch_assoc()) { $meses_data[] = $row; }
?>
<script>
$(function(){
    var salas = <?php echo json_encode($salas_data) ?>;
    var meses = <?php echo json_encode($meses_data) ?>;

    new Chart(document.getElementById('chart_salas').getContext('2d'), {
        type: 'bar',
        data: {
            labels: salas.map(s => s.room_name),
            datasets: [{
                label: 'Número de reservas',
                data: salas.map(s => s.total),
                backgroundColor: 'rgba(60,141,188,0.7)'
            }]
        },
        options: {
            scales: { yAxes: [{ ticks: { beginAtZero: true, precision: 0 } }] },
            legend: { display: false }
        }
    });

    new Chart(document.getElementById('chart_meses').getContext('2d'), {
        type: 'line',
        data: {
            labels: meses.map(m => m.ym),
            datasets: [{
                label: 'Reservas',
                data: meses.map(m => m.total),
                borderColor: 'rgba(0,166,90,1)',
                backgroundColor: 'rgba(0,166,90,0.2)',
                fill: true
            }]
        },
        options: {
            scales: { yAxes: [{ ticks: { beginAtZero: true, precision: 0 } }] },
            legend: { display: false }
        }
    });
});
</script>