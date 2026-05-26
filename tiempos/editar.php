<?php
include("../includes/header.php");
include("../conexion.php");
$db = (new Cconexion())->conexionBD();
$id = intval($_GET['id'] ?? 0);
if (!$id) { header("Location: lista.php"); exit; }
$stmt = $db->prepare("SELECT * FROM registro_tiempos WHERE id=:id");
$stmt->execute([':id'=>$id]);
$rt = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$rt) { header("Location: lista.php"); exit; }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $db->prepare("UPDATE registro_tiempos SET tarea_id=:tarea, usuario_id=:usuario, fecha=:fecha, horas_trabajadas=:horas, descripcion=:desc WHERE id=:id");
    $stmt->execute([
        ':tarea' => $_POST['tarea_id'] ?: null,
        ':usuario' => $_POST['usuario_id'] ?: null,
        ':fecha' => $_POST['fecha'],
        ':horas' => $_POST['horas_trabajadas'],
        ':desc' => $_POST['descripcion'],
        ':id' => $id
    ]);
    header("Location: lista.php?msg=actualizado");
    exit;
}

$tareas = $db->query("SELECT t.id, t.nombre, p.nombre AS proyecto FROM tareas t LEFT JOIN proyectos p ON t.proyecto_id = p.id ORDER BY p.nombre, t.nombre")->fetchAll(PDO::FETCH_ASSOC);
$usuarios = $db->query("SELECT id, nombre, apellido FROM usuarios WHERE activo=1 ORDER BY nombre")->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="page-header">
    <div><div class="page-title">Editar Registro de Tiempo</div></div>
    <a href="lista.php" class="btn btn-ghost">← Volver</a>
</div>

<div class="card">
    <form method="POST">
        <div class="form-grid">
            <div class="form-group">
                <label>Tarea</label>
                <select name="tarea_id">
                    <option value="">— Seleccionar —</option>
                    <?php foreach ($tareas as $t): ?>
                        <option value="<?= $t['id'] ?>" <?= $rt['tarea_id'] == $t['id'] ? 'selected' : '' ?>><?= htmlspecialchars($t['proyecto'] . ' › ' . $t['nombre']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Usuario</label>
                <select name="usuario_id">
                    <option value="">— Seleccionar —</option>
                    <?php foreach ($usuarios as $u): ?>
                        <option value="<?= $u['id'] ?>" <?= $rt['usuario_id'] == $u['id'] ? 'selected' : '' ?>><?= htmlspecialchars($u['nombre'] . ' ' . $u['apellido']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Fecha</label>
                <input type="date" name="fecha" value="<?= date('Y-m-d', strtotime($rt['fecha'])) ?>">
            </div>
            <div class="form-group">
                <label>Horas Trabajadas</label>
                <input type="number" name="horas_trabajadas" min="0.5" step="0.5" value="<?= $rt['horas_trabajadas'] ?>">
            </div>
            <div class="form-group full">
                <label>Descripción</label>
                <textarea name="descripcion"><?= htmlspecialchars($rt['descripcion']) ?></textarea>
            </div>
        </div>
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Guardar Cambios</button>
            <a href="lista.php" class="btn btn-ghost">Cancelar</a>
        </div>
    </form>
</div>

<?php include("../includes/footer.php"); ?>
