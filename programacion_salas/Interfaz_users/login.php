<?php require_once('config.php'); require_once('inc/sess_auth.php'); ?>
<!DOCTYPE html>
<html lang="es">
<?php require_once('inc/header.php') ?>
<body>
    <div class="portal-wrap" style="max-width:420px;">
        <div class="card mt-5 shadow-sm">
            <div class="card-body p-4">
                <h4 class="mb-1 text-center">Portal de Reservas</h4>
                <p class="text-muted text-center mb-4">Ingresa con tu correo institucional</p>
                <form id="login_frm">
                    <div class="form-group">
                        <label>Correo institucional</label>
                        <input type="email" name="institutional_email" class="form-control" required placeholder="nombre<?php echo DOMINIO_ESTUDIANTES ?>">
                    </div>
                    <div class="form-group">
                        <label>Contraseña</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <div class="err-box"></div>
                    <button class="btn btn-primary btn-block mt-2">Ingresar</button>
                </form>
               
                <p class="text-center mt-3 mb-0">
                    <a href="<?php echo base_url ?>Interfaz_users/register.php">¿No tienes cuenta? Registrate</a>
                </p>
            </div>
        </div>
    </div>
    <script>
    $('#login_frm').submit(function(e){
        e.preventDefault();
        $('.err-box').html('');
        $.ajax({
            url: _base_url_ + 'Interfaz_users/clases/portalAuth.php?f=login',
            method: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(resp){
                if(resp.status == 'success'){
                    location.href = _base_url_ + 'Interfaz_users/index.php';
                } else {
                    $('.err-box').html('<div class="alert alert-danger py-2">'+resp.msg+'</div>');
                }
            },
            error: function(){
                $('.err-box').html('<div class="alert alert-danger py-2">Ocurrio un error. Intenta de nuevo.</div>');
            }
        });
    });
    </script>
</body>
</html>
