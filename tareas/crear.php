<?php
include("../includes/header.php");
include("../conexion.php");
$db = (new Cconexion())->conexionBD();

$proyecto_presel = intval($_GET['proyecto_id'] ?? 0);
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre']);
    if (!$nombre) {
        $error = 'El nombre de la tarea es requerido.';
    } else {
        $stmt = $db->prepare("INSERT INTO tareas (proyecto_id, nombre, descripcion, asignado_a, estado, prioridad, horas_estimadas, horas_reales, creado_en)
            VALUES (:pid, :nombre, :desc, :asignado, :estado, :prioridad, :h_est, :h_real, GETDATE())");
        $stmt->execute([
            ':pid' => $_POST['proyecto_id'] ?: null,
            ':nombre' => $nombre,
            ':desc' => $_POST['descripcion'],
            ':asignado' => $_POST['asignado_a'] ?: null,
            ':estado' => $_POST['estado'],
            ':prioridad' => $_POST['prioridad'],
            ':h_est' => $_POST['horas_estimadas'] ?: 0,
            ':h_real' => $_POST['horas_reales'] ?: 0,
        ]);
        header("Location: lista.php?msg=creado");
        exit;
    }
}

$proyectos = $db->query("SELECT id, nombre FROM proyectos ORDER BY nombre")->fetchAll(PDO::FETCH_ASSOC);
$usuarios = $db->query("SELECT id, nombre, apellido FROM usuarios WHERE activo=1 ORDER BY nombre")->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="page-header">
    <div>
        <div class="page-title">Nueva Tarea</div>
        <div class="page-subtitle">Registra una tarea en un proyecto</div>
    </div>
    <a href="lista.php" class="btn btn-ghost">← Volver</a>
</div>

<?php if ($error): ?><div class="alert alert-error">✕ <?= $error ?></div><?php endif; ?>

<div class="card">
    <form method="POST">
        <div class="form-grid">
            <div class="form-group full">
                <label>Nombre de la Tarea *</label>
                <input type="text" name="nombre" placeholder="Ej: Diseño de wireframes">
            </div>
            <div class="form-group full">
                <label>Descripción</label>
                <textarea name="descripcion" placeholder="Detalle de la tarea..."></textarea>
            </div>
            <div class="form-group">
                <label>Proyecto</label>
                <select name="proyecto_id">
                    <option value="">— Sin proyecto —</option>
                    <?php foreach ($proyectos as $pr): ?>
                        <option value="<?= $pr['id'] ?>" <?= $proyecto_presel == $pr['id'] ? 'selected' : '' ?>><?= htmlspecialchars($pr['nombre']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Asignado a</label>
                <select name="asignado_a">
                    <option value="">— Sin asignar —</option>
                    <?php foreach ($usuarios as $u): ?>
                        <option value="<?= $u['id'] ?>"><?= htmlspecialchars($u['nombre'] . ' ' . $u['apellido']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Estado</label>
                <select name="estado">
                    <option value="pendiente">Pendiente</option>
                    <option value="en_progreso">En Progreso</option>
                    <option value="completado">Completado</option>
                </select>
            </div>
            <div class="form-group">
                <label>Prioridad</label>
                <select name="prioridad">
                    <option value="media">Media</option>
                    <option value="alta">Alta</option>
                    <option value="baja">Baja</option>
                </select>
            </div>
            <div class="form-group">
                <label>Horas Estimadas</label>
                <input type="number" name="horas_estimadas" min="0" step="0.5" value="0">
            </div>
            <div class="form-group">
                <label>Horas Reales</label>
                <input type="number" name="horas_reales" min="0" step="0.5" value="0">
            </div>
        </div>
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Guardar Tarea</button>
            <a href="lista.php" class="btn btn-ghost">Cancelar</a>
        </div>
    </form>
</div>

<?php include("../includes/footer.php"); ?>
