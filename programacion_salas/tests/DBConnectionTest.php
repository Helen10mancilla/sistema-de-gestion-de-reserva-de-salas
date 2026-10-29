<?php

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../classes/DBConnection.php';

class DBConnectionTest extends TestCase
{
    public function testConexionConBaseDeDatos(): void
    {
        $db = new DBConnection();

        $this->assertInstanceOf(
            mysqli::class,
            $db->conn
        );

        $this->assertSame(
            0,
            $db->conn->connect_errno,
            'La conexión a la base de datos no está operativa.'
        );
    }
}