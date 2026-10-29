
<?php

use PHPUnit\Framework\TestCase;

class PortalAuthTest extends TestCase
{
    private function ejecutarRegistro(array $datos): array
    {
        $archivoTemporal = tempnam(sys_get_temp_dir(), 'portal_test_');
        $archivoDatos = tempnam(sys_get_temp_dir(), 'portal_datos_');

        $codigo = <<<'PHP'
<?php
$_GET['f'] = 'registrar';
$_POST = json_decode(file_get_contents($argv[2]), true) ?: [];

require $argv[1];
PHP;

        file_put_contents($archivoTemporal, $codigo);
        file_put_contents($archivoDatos, json_encode($datos));

        try {
            $comando = escapeshellarg(PHP_BINARY) . ' '
                . escapeshellarg($archivoTemporal) . ' '
                . escapeshellarg(
                    __DIR__ . '/../Interfaz_users/clases/portalAuth.php'
                ) . ' '
                . escapeshellarg($archivoDatos);

            exec($comando . ' 2>&1', $salida, $codigoSalida);

            $salidaCompleta = implode("\n", $salida);
            $respuesta = json_decode($salidaCompleta, true);

            $this->assertSame(
                0,
                $codigoSalida,
                'Error al ejecutar portalAuth.php: ' . $salidaCompleta
            );

            $this->assertIsArray(
                $respuesta,
                'La respuesta no es JSON válido: ' . $salidaCompleta
            );

            return $respuesta;
        } finally {
            if (file_exists($archivoTemporal)) {
                unlink($archivoTemporal);
            }

            if (file_exists($archivoDatos)) {
                unlink($archivoDatos);
            }
        }
    }

    public function testRegistroRechazaCamposObligatoriosVacios(): void
    {
        $respuesta = $this->ejecutarRegistro([
            'institutional_email' => '',
            'password' => '',
            'fullname' => ''
        ]);

        $this->assertSame('failed', $respuesta['status'] ?? null);
        $this->assertSame(
            'Todos los campos son obligatorios.',
            $respuesta['msg'] ?? null
        );
    }

    public function testRegistroRechazaContrasenaCorta(): void
    {
        $respuesta = $this->ejecutarRegistro([
            'institutional_email' => 'prueba@estudiante.uniajc.edu.co',
            'password' => '12345',
            'fullname' => 'Usuario de prueba'
        ]);

        $this->assertSame('failed', $respuesta['status'] ?? null);
        $this->assertSame(
            'La contraseña debe tener al menos 6 caracteres.',
            $respuesta['msg'] ?? null
        );
    }
    public function testRegistroRechazaCorreoNoInstitucional(): void
{
    $respuesta = $this->ejecutarRegistro([
        'institutional_email' => 'usuario@gmail.com',
        'password' => 'ClavePrueba123',
        'fullname' => 'Usuario de prueba'
    ]);

    $this->assertSame(
        'failed',
        $respuesta['status'] ?? null
    );
}
private function ejecutarAccion(string $accion, array $datos = []): array
{
    $archivoTemporal = tempnam(sys_get_temp_dir(), 'portal_test_');
    $archivoDatos = tempnam(sys_get_temp_dir(), 'portal_datos_');

    $codigo = <<<'PHP'
<?php
$_GET['f'] = $argv[2];
$_POST = json_decode(file_get_contents($argv[3]), true) ?: [];

require $argv[1];
PHP;

    file_put_contents($archivoTemporal, $codigo);
    file_put_contents($archivoDatos, json_encode($datos));

    try {
        $comando = escapeshellarg(PHP_BINARY) . ' '
            . escapeshellarg($archivoTemporal) . ' '
            . escapeshellarg(
                __DIR__ . '/../Interfaz_users/clases/portalAuth.php'
            ) . ' '
            . escapeshellarg($accion) . ' '
            . escapeshellarg($archivoDatos);

        exec($comando . ' 2>&1', $salida, $codigoSalida);

        $salidaCompleta = implode("\n", $salida);
        $respuesta = json_decode($salidaCompleta, true);

        $this->assertSame(
            0,
            $codigoSalida,
            'Error al ejecutar portalAuth.php: ' . $salidaCompleta
        );

        $this->assertIsArray(
            $respuesta,
            'La respuesta no es JSON válido: ' . $salidaCompleta
        );

        return $respuesta;
    } finally {
        if (file_exists($archivoTemporal)) {
            unlink($archivoTemporal);
        }

        if (file_exists($archivoDatos)) {
            unlink($archivoDatos);
        }
    }
}
public function testLoginRechazaCredencialesIncorrectas(): void
{
    $respuesta = $this->ejecutarAccion('login', [
        'institutional_email' => 'usuario_inexistente_prueba@gmail.com',
        'password' => 'ClaveIncorrecta123'
    ]);

    $this->assertSame('failed', $respuesta['status'] ?? null);
    $this->assertSame(
        'Correo o contraseña incorrectos.',
        $respuesta['msg'] ?? null
    );
}




public function testLogoutEliminaDatosDeSesionDelPortal(): void
{
    $archivoTemporal = tempnam(sys_get_temp_dir(), 'portal_logout_');

    $codigo = <<<'PHP'
<?php
require $argv[2];

$_SESSION['portal_userdata'] = [
    'id' => 999999,
    'email' => 'usuario_prueba@estudiante.uniajc.edu.co',
    'fullname' => 'Usuario de prueba',
    'role' => 'student'
];

$_GET['f'] = 'logout';
$_POST = [];

ob_start();
require $argv[1];
$respuesta = ob_get_clean();

echo json_encode([
    'respuesta' => json_decode($respuesta, true),
    'sesion_eliminada' => !isset($_SESSION['portal_userdata'])
]);
PHP;

    file_put_contents($archivoTemporal, $codigo);

    try {
        $comando = escapeshellarg(PHP_BINARY) . ' '
            . escapeshellarg($archivoTemporal) . ' '
            . escapeshellarg(
                __DIR__ . '/../Interfaz_users/clases/portalAuth.php'
            ) . ' '
            . escapeshellarg(__DIR__ . '/../config.php');

        exec($comando . ' 2>&1', $salida, $codigoSalida);

        $salidaCompleta = implode("\n", $salida);
        $datos = json_decode($salidaCompleta, true);

        $this->assertSame(
            0,
            $codigoSalida,
            'Error al ejecutar el cierre de sesión: ' . $salidaCompleta
        );

        $this->assertIsArray(
            $datos,
            'La salida no es JSON válido: ' . $salidaCompleta
        );

        $this->assertSame(
            'success',
            $datos['respuesta']['status'] ?? null
        );

        $this->assertTrue(
            $datos['sesion_eliminada'] ?? false,
            'La sesión autenticada de prueba no fue eliminada.'
        );
    } finally {
        if (file_exists($archivoTemporal)) {
            unlink($archivoTemporal);
        }
    }
}



}

