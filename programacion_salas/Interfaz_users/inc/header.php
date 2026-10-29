<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Reserva de espacios fisicos | Biblioteca UNICAMACHO</title>
    <link rel="icon" type="image/png" href="<?php echo validate_image($_settings->info('logo')) ?>">
    <link rel="stylesheet" href="<?php echo base_url ?>plugins/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="<?php echo base_url ?>plugins/select2/css/select2.min.css">
    <link rel="stylesheet" href="<?php echo base_url ?>plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">
    <link rel="stylesheet" href="<?php echo base_url ?>dist/css/adminlte.css">
    <link rel="stylesheet" href="<?php echo base_url ?>dist/css/custom.css">
    <link rel="stylesheet" href="<?php echo base_url ?>plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css">
    <link rel="stylesheet" href="<?php echo base_url ?>plugins/fullcalendar/main.css">
    <link rel="stylesheet" href="<?php echo base_url ?>plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css">

    <script src="<?php echo base_url ?>plugins/jquery/jquery.min.js"></script>
    <script src="<?php echo base_url ?>plugins/jquery-ui/jquery-ui.min.js"></script>
    <script src="<?php echo base_url ?>plugins/sweetalert2/sweetalert2.min.js"></script>
    <script src="<?php echo base_url ?>plugins/toastr/toastr.min.js"></script>
    <script src="<?php echo base_url ?>plugins/moment/moment.min.js"></script>
    <script src="<?php echo base_url ?>plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js"></script>
    <script src="<?php echo base_url ?>plugins/fullcalendar/main.js"></script>
    <script src="<?php echo base_url ?>plugins/select2/js/select2.full.min.js"></script>
    <script>
        var _base_url_ = '<?php echo base_url ?>';
        function alert_toast(msg, type) {
            toastr[type] ? toastr[type](msg) : toastr.info(msg);
        }
        function start_loader() { $('body').css('cursor', 'wait'); }
        function end_loader() { $('body').css('cursor', 'default'); }
    </script>
    <style>
        body { background: #f4f6f9; }
        .portal-navbar { background:#343a40; padding:.8rem 1.5rem; color:#fff; display:flex; justify-content:space-between; align-items:center; }
        .portal-navbar a { color:#fff; text-decoration:none; }
        .portal-wrap { max-width: 1100px; margin: 20px auto; padding: 0 15px; }
    </style>
</head>