<?php
include("../includes/header.php");
requiereRol([ROL_ADMIN, ROL_GERENTE]);
include("../conexion.php");
$db = (new Cconexion())->conexionBD();

$error = '';
$exito = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre']);
    $descripcion = trim($_POST['descripcion']);
    $cliente_id = $_POST['cliente_id'] ?: null;
    $gerente_id = $_POST['gerente_id'] ?: null;
    $estado = $_POST['estado'];
    $prioridad = $_POST['prioridad'];
    $fecha_inicio = $_POST['fecha_inicio'] ?: null;
    $fecha_fin = $_POST['fecha_fin'] ?: null;
    $presupuesto = $_POST['presupuesto'] ?: 0;
    $avance = $_POST['porcentaje_avance'] ?: 0;

    if (!$nombre) {
        $error = 'El nombre del proyecto es requerido.';
    } else {
        $stmt = $db->prepare("INSERT INTO proyectos (nombre, descripcion, cliente_id, gerente_id, estado, prioridad, fecha_inicio, fecha_fin, presupuesto, porcentaje_avance, creado_en)
            VALUES (:nombre, :desc, :cliente, :gerente, :estado, :prioridad, :inicio, :fin, :presupuesto, :avance, GETDATE())");
        $stmt->execute([
            ':nombre' => $nombre, ':desc' => $descripcion,
            ':cliente' => $cliente_id, ':gerente' => $gerente_id,
            ':estado' => $estado, ':prioridad' => $prioridad,
            ':inicio' => $fecha_inicio, ':fin' => $fecha_fin,
            ':presupuesto' => $presupuesto, ':avance' => $avance
        ]);
        header("Location: lista.php?msg=creado");
        exit;
    }
}

$clientes = $db->query("SELECT id, nombre FROM clientes ORDER BY nombre")->fetchAll(PDO::FETCH_ASSOC);
$usuarios = $db->query("SELECT id, nombre, apellido FROM usuarios WHERE activo=1 ORDER BY nombre")->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="page-header">
    <div>
        <div class="page-title">Nuevo Proyecto</div>
        <div class="page-subtitle">Completa los datos del proyecto</div>
    </div>
    <a href="lista.php" class="btn btn-ghost">← Volver</a>
</div>

<?php if ($error): ?><div class="alert alert-error">✕ <?= $error ?></div><?php endif; ?>

<div class="card">
    <form method="POST">
        <div class="form-grid">
            <div class="form-group full">
                <label>Nombre del Proyecto *</label>
                <input type="text" name="nombre" value="<?= htmlspecialchars($_POST['nombre'] ?? '') ?>" placeholder="Ej: Rediseño de sitio web">
            </div>
            <div class="form-group full">
                <label>Descripción</label>
                <textarea name="descripcion" placeholder="Describe el alcance del proyecto..."><?= htmlspecialchars($_POST['descripcion'] ?? '') ?></textarea>
            </div>
            <div class="form-group">
                <label>Cliente</label>
                <select name="cliente_id">
                    <option value="">— Sin cliente —</option>
                    <?php foreach ($clientes as $c): ?>
                        <option value="<?= $c['id'] ?>" <?= ($_POST['cliente_id'] ?? '') == $c['id'] ? 'selected' : '' ?>><?= htmlspecialchars($c['nombre']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Gerente / Responsable</label>
                <select name="gerente_id">
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
                    <option value="cancelado">Cancelado</option>
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
                <label>Fecha de Inicio</label>
                <input type="date" name="fecha_inicio" value="<?= $_POST['fecha_inicio'] ?? date('Y-m-d') ?>">
            </div>
            <div class="form-group">
                <label>Fecha de Fin</label>
                <input type="date" name="fecha_fin" value="<?= $_POST['fecha_fin'] ?? '' ?>">
            </div>
            <div class="form-group">
                <label>Presupuesto ($)</label>
                <input type="number" name="presupuesto" step="0.01" min="0" value="<?= $_POST['presupuesto'] ?? '0' ?>">
            </div>
            <div class="form-group">
                <label>% Avance Inicial</label>
                <input type="number" name="porcentaje_avance" min="0" max="100" step="0.01" value="<?= $_POST['porcentaje_avance'] ?? '0' ?>">
            </div>
        </div>
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Guardar Proyecto</button>
            <a href="lista.php" class="btn btn-ghost">Cancelar</a>
        </div>
    </form>
</div>

<?php include("../includes/footer.php"); ?>
