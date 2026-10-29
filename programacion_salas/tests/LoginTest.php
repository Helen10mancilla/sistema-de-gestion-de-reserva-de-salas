

<?php

use PHPUnit\Framework\TestCase;

class LoginTest extends TestCase
{
    public function testInicioSesionConCredencialesIncorrectas(): void
    {
        $codigo = <<<'PHP'
<?php
$_GET['f'] = 'login';
$_POST = [
    'username' => '__phpunit_usuario_inexistente__',
    'password' => '__clave_invalida__'
];

require $argv[1];
PHP;

        $archivoTemporal = tempnam(sys_get_temp_dir(), 'login_test_');
        file_put_contents($archivoTemporal, $codigo);

        try {
            $comando = escapeshellarg(PHP_BINARY) . ' '
                . escapeshellarg($archivoTemporal) . ' '
                . escapeshellarg(__DIR__ . '/../classes/Login.php');

            exec($comando . ' 2>&1', $salida, $codigoSalida);

            $respuesta = implode("\n", $salida);
            $datos = json_decode($respuesta, true);

            $this->assertSame(
                0,
                $codigoSalida,
                'Error al ejecutar Login.php: ' . $respuesta
            );

            $this->assertIsArray(
                $datos,
                'La respuesta no es JSON válido: ' . $respuesta
            );

            $this->assertSame(
                'incorrect',
                $datos['status'] ?? null
            );
        } finally {
            if (file_exists($archivoTemporal)) {
                unlink($archivoTemporal);
            }
        }
    }
}


