<?php
include("../conexion.php");
$db = (new Cconexion())->conexionBD();
$id = intval($_GET['id'] ?? 0);
if ($id) {
    $db->prepare("DELETE FROM gastos WHERE proyecto_id=:id")->execute([':id'=>$id]);
    $db->prepare("DELETE FROM tareas WHERE proyecto_id=:id")->execute([':id'=>$id]);
    $db->prepare("DELETE FROM proyectos WHERE id=:id")->execute([':id'=>$id]);
}
header("Location: lista.php?msg=eliminado");
exit;
