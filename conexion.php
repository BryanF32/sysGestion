<?php
class Cconexion {
    function conexionBD() {
        $host = "localhost";
        $bd = "db_proyectos";
        $usuario = "s_gestion";
        $password = "123456";
        try {
            $conexion = new PDO(
                "sqlsrv:Server=$host;Database=$bd;TrustServerCertificate=1", $usuario, $password
            );
            $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $conexion;
        } catch (PDOException $e) {
            die("Error de conexión: " . $e->getMessage());
        }
    }
}
?>
