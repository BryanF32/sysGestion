<?php
include("../conexion.php");
$db = (new Cconexion())->conexionBD();
$id = intval($_GET['id'] ?? 0);
if ($id) $db->prepare("DELETE FROM registro_tiempos WHERE id=:id")->execute([':id'=>$id]);
header("Location: lista.php?msg=eliminado");
exit;
