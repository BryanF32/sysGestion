<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "Paso 1: PHP OK<br>";

if (!extension_loaded('pdo_sqlsrv')) {
    die("ERROR: El driver pdo_sqlsrv NO está cargado");
}
echo "Paso 2: Driver pdo_sqlsrv OK<br>";

try {
    $con = new PDO(
        "sqlsrv:Server=localhost;Database=db_proyectos;TrustServerCertificate=1",
        "s_gestion",
        "123456"
    );
    echo "Paso 3: Conexión a BD OK ✅";
} catch (PDOException $e) {
    echo "ERROR conexión: " . $e->getMessage();
}
?>