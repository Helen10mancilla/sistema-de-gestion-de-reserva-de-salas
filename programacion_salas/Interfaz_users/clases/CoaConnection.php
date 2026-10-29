<?php

class CoaConnection {

    private $conn = null;

    public function __construct() {
        if (!COA_MODO_PRUEBA) {
            $this->conn = new mysqli(COA_DB_HOST, COA_DB_USER, COA_DB_PASS, COA_DB_NAME);
            if ($this->conn->connect_error) {
                $this->conn = null;
            }
        }
    }

    private function buscar_persona($email) {
        if (!$this->conn) return null;
        $email_esc = $this->conn->real_escape_string($email);
        $sql = "SELECT nombre_completo, codigo, tipo_persona FROM personas WHERE correo_institucional = '{$email_esc}' LIMIT 1";
        $result = @$this->conn->query($sql);
        if ($result && $result->num_rows > 0) {
            return $result->fetch_assoc();
        }
        return null;
    }

    public function validar($email) {
        $email = trim(strtolower($email));

        $es_estudiante = str_ends_with($email, strtolower(DOMINIO_ESTUDIANTES));
        $es_docente = str_ends_with($email, strtolower(DOMINIO_DOCENTES));

        if (!$es_estudiante && !$es_docente) {
            return [
                'valido' => false,
                'rol' => null,
                'nombre' => null,
                'coa_ref_id' => null,
                'mensaje' => 'El correo debe ser institucional (' . DOMINIO_ESTUDIANTES . ' o ' . DOMINIO_DOCENTES . ').'
            ];
        }

        $rol = $es_estudiante ? 'student' : 'faculty';

        if (COA_MODO_PRUEBA) {
            return [
                'valido' => true,
                'rol' => $rol,
                'nombre' => null,
                'coa_ref_id' => null,
                'mensaje' => 'OK (modo prueba)'
            ];
        }

        $persona = $this->buscar_persona($email);
        if (!$persona) {
            return [
                'valido' => false,
                'rol' => null,
                'nombre' => null,
                'coa_ref_id' => null,
                'mensaje' => 'No encontramos ese correo registrado en el sistema de la biblioteca.'
            ];
        }

        return [
            'valido' => true,
            'rol' => $rol,
            'nombre' => $persona['nombre_completo'] ?? null,
            'coa_ref_id' => $persona['codigo'] ?? null,
            'mensaje' => 'ok'
        ];
    }

    public function __destruct() {
        if ($this->conn) $this->conn->close();
    }
}
?>
