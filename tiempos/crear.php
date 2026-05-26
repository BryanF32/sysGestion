<?php
include("../includes/header.php");
include("../conexion.php");
$db = (new Cconexion())->conexionBD();
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $db->prepare("INSERT INTO registro_tiempos (tarea_id, usuario_id, fecha, horas_trabajadas, descripcion)
        VALUES (:tarea, :usuario, :fecha, :horas, :desc)");
    $stmt->execute([
        ':tarea' => $_POST['tarea_id'] ?: null,
        ':usuario' => $_POST['usuario_id'] ?: null,
        ':fecha' => $_POST['fecha'],
        ':horas' => $_POST['horas_trabajadas'],
        ':desc' => $_POST['descripcion'],
    ]);
    header("Location: lista.php?msg=creado");
    exit;
}

$tareas = $db->query("SELECT t.id, t.nombre, p.nombre AS proyecto FROM tareas t LEFT JOIN proyectos p ON t.proyecto_id = p.id ORDER BY p.nombre, t.nombre")->fetchAll(PDO::FETCH_ASSOC);
$usuarios = $db->query("SELECT id, nombre, apellido FROM usuarios WHERE activo=1 ORDER BY nombre")->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="page-header">
    <div>
        <div class="page-title">Registrar Tiempo</div>
        <div class="page-subtitle">Registra horas trabajadas en una tarea</div>
    </div>
    <a href="lista.php" class="btn btn-ghost">← Volver</a>
</div>

<div class="card">
    <form method="POST">
        <div class="form-grid">
            <div class="form-group">
                <label>Tarea</label>
                <select name="tarea_id">
                    <option value="">— Seleccionar tarea —</option>
                    <?php foreach ($tareas as $t): ?>
                        <option value="<?= $t['id'] ?>"><?= htmlspecialchars($t['proyecto'] . ' › ' . $t['nombre']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Usuario</label>
                <select name="usuario_id">
                    <option value="">— Seleccionar usuario —</option>
                    <?php foreach ($usuarios as $u): ?>
                        <option value="<?= $u['id'] ?>"><?= htmlspecialchars($u['nombre'] . ' ' . $u['apellido']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Fecha</label>
                <input type="date" name="fecha" value="<?= date('Y-m-d') ?>">
            </div>
            <div class="form-group">
                <label>Horas Trabajadas</label>
                <input type="number" name="horas_trabajadas" min="0.5" step="0.5" value="1" required>
            </div>
            <div class="form-group full">
                <label>Descripción / Actividad realizada</label>
                <textarea name="descripcion" placeholder="¿Qué se hizo en estas horas?"></textarea>
            </div>
        </div>
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Guardar Registro</button>
            <a href="lista.php" class="btn btn-ghost">Cancelar</a>
        </div>
    </form>
</div>

<?php include("../includes/footer.php"); ?>
