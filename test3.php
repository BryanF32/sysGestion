<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "Linea 1<br>";

include("conexion.php");
echo "Linea 2<br>";

$db = (new Cconexion())->conexionBD();
echo "Linea 3 - BD OK<br>";

include("includes/header.php");
echo "Linea 4 - Header OK<br>";
?>