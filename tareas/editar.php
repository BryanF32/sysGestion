<?php
include("../includes/header.php");
include("../conexion.php");
$db = (new Cconexion())->conexionBD();
$id = intval($_GET['id'] ?? 0);
if (!$id) { header("Location: lista.php"); exit; }
$stmt = $db->prepare("SELECT * FROM tareas WHERE id=:id");
$stmt->execute([':id'=>$id]);
$t = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$t) { header("Location: lista.php"); exit; }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $db->prepare("UPDATE tareas SET proyecto_id=:pid, nombre=:nombre, descripcion=:desc, asignado_a=:asignado, estado=:estado, prioridad=:prioridad, horas_estimadas=:h_est, horas_reales=:h_real WHERE id=:id");
    $stmt->execute([
        ':pid' => $_POST['proyecto_id'] ?: null,
        ':nombre' => trim($_POST['nombre']),
        ':desc' => $_POST['descripcion'],
        ':asignado' => $_POST['asignado_a'] ?: null,
        ':estado' => $_POST['estado'],
        ':prioridad' => $_POST['prioridad'],
        ':h_est' => $_POST['horas_estimadas'] ?: 0,
        ':h_real' => $_POST['horas_reales'] ?: 0,
        ':id' => $id
    ]);
    header("Location: lista.php?msg=actualizado");
    exit;
}

$proyectos = $db->query("SELECT id, nombre FROM proyectos ORDER BY nombre")->fetchAll(PDO::FETCH_ASSOC);
$usuarios = $db->query("SELECT id, nombre, apellido FROM usuarios WHERE activo=1 ORDER BY nombre")->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="page-header">
    <div><div class="page-title">Editar Tarea</div></div>
    <a href="lista.php" class="btn btn-ghost">← Volver</a>
</div>

<div class="card">
    <form method="POST">
        <div class="form-grid">
            <div class="form-group full">
                <label>Nombre *</label>
                <input type="text" name="nombre" value="<?= htmlspecialchars($t['nombre']) ?>">
            </div>
            <div class="form-group full">
                <label>Descripción</label>
                <textarea name="descripcion"><?= htmlspecialchars($t['descripcion']) ?></textarea>
            </div>
            <div class="form-group">
                <label>Proyecto</label>
                <select name="proyecto_id">
                    <option value="">— Sin proyecto —</option>
                    <?php foreach ($proyectos as $pr): ?>
                        <option value="<?= $pr['id'] ?>" <?= $t['proyecto_id'] == $pr['id'] ? 'selected' : '' ?>><?= htmlspecialchars($pr['nombre']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Asignado a</label>
                <select name="asignado_a">
                    <option value="">— Sin asignar —</option>
                    <?php foreach ($usuarios as $u): ?>
                        <option value="<?= $u['id'] ?>" <?= $t['asignado_a'] == $u['id'] ? 'selected' : '' ?>><?= htmlspecialchars($u['nombre'] . ' ' . $u['apellido']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Estado</label>
                <select name="estado">
                    <?php foreach (['pendiente','en_progreso','completado'] as $e): ?>
                        <option value="<?= $e ?>" <?= $t['estado'] == $e ? 'selected' : '' ?>><?= str_replace('_',' ',ucfirst($e)) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Prioridad</label>
                <select name="prioridad">
                    <?php foreach (['alta','media','baja'] as $pr): ?>
                        <option value="<?= $pr ?>" <?= $t['prioridad'] == $pr ? 'selected' : '' ?>><?= ucfirst($pr) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Horas Estimadas</label>
                <input type="number" name="horas_estimadas" min="0" step="0.5" value="<?= $t['horas_estimadas'] ?>">
            </div>
            <div class="form-group">
                <label>Horas Reales</label>
                <input type="number" name="horas_reales" min="0" step="0.5" value="<?= $t['horas_reales'] ?>">
            </div>
        </div>
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Guardar Cambios</button>
            <a href="lista.php" class="btn btn-ghost">Cancelar</a>
        </div>
    </form>
</div>

<?php include("../includes/footer.php"); ?>
