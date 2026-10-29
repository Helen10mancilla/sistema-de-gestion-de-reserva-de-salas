
<?php

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../classes/DBConnection.php';
require_once __DIR__ . '/../classes/SystemSettings.php';

class SystemSettingsTest extends TestCase
{
    private SystemSettings $settings;

    protected function setUp(): void
    {
        $_SESSION = [];

        $this->settings = new SystemSettings();
    }

    protected function tearDown(): void
    {
        unset($this->settings);
        $_SESSION = [];
    }

    public function testGuardarYConsultarDatoDeUsuario(): void
    {
        $this->settings->set_userdata(
            'username',
            'usuario_prueba'
        );

        $this->assertSame(
            'usuario_prueba',
            $this->settings->userdata('username')
        );
    }

    public function testConsultarDatoDeUsuarioInexistente(): void
    {
        $this->assertNull(
            $this->settings->userdata('usuario_inexistente')
        );
    }

    public function testCerrarSesionEliminaDatosDelUsuario(): void
    {
        $this->settings->set_userdata(
            'username',
            'usuario_prueba'
        );

        $resultado = $this->settings->sess_des();

        $this->assertTrue($resultado);
        $this->assertArrayNotHasKey('userdata', $_SESSION);
    }
}


