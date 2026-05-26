<?php
include("../includes/auth.php");
include("../conexion.php");
$db = (new Cconexion())->conexionBD();
$id = intval($_GET['id'] ?? 0);
if ($id) {
    $db->prepare("DELETE FROM registro_tiempos WHERE tarea_id=:id")->execute([':id'=>$id]);
    $db->prepare("DELETE FROM tareas WHERE id=:id")->execute([':id'=>$id]);
}
header("Location: lista.php?msg=eliminado");
exit;
