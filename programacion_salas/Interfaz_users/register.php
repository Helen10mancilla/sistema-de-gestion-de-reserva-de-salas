<?php require_once('config.php'); require_once('inc/sess_auth.php'); ?>
<!DOCTYPE html>
<html lang="es">
<?php require_once('inc/header.php') ?>
<body>
    <div class="portal-wrap" style="max-width:460px;">
        <div class="card mt-5 shadow-sm">
            <div class="card-body p-4">
                <h4 class="mb-1 text-center">Crear cuenta</h4>
                <p class="text-muted text-center mb-4">Solo con tu correo institucional</p>
                <?php if (COA_MODO_PRUEBA): ?>
                
                <?php endif; ?>
                <form id="register_frm">
                    <div class="form-group">
                        <label>Nombre completo</label>
                        <input type="text" name="fullname" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Correo institucional</label>
                        <input type="email" name="institutional_email" class="form-control" required
                               placeholder="nombre<?php echo DOMINIO_ESTUDIANTES ?>">
                        <small class="text-muted">Debe terminar en <?php echo DOMINIO_ESTUDIANTES ?> o <?php echo DOMINIO_DOCENTES ?></small>
                    </div>
                    <div class="form-group">
                        <label>Contraseña</label>
                        <input type="password" name="password" class="form-control" required minlength="6">
                    </div>
                    <div class="err-box"></div>
                    <button class="btn btn-primary btn-block mt-2">Registrarme</button>
                </form>
                <p class="text-center mt-3 mb-0">
                    <a href="<?php echo base_url ?>Interfaz_users/login.php">Ya tengo cuenta, iniciar sesion</a>
                </p>
            </div>
        </div>
    </div>
    <script>
    $('#register_frm').submit(function(e){
        e.preventDefault();
        $('.err-box').html('');
        $.ajax({
            url: _base_url_ + 'Interfaz_users/clases/portalAuth.php?f=registrar',
            method: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(resp){
                if(resp.status == 'success'){
                    alert_toast(resp.msg,'success');
                    setTimeout(()=> location.href = _base_url_ + 'Interfaz_users/login.php', 1200);
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