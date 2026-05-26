<?php
include("../includes/header.php");
include("../conexion.php");
$db = (new Cconexion())->conexionBD();

$id = intval($_GET['id'] ?? 0);
if (!$id) { header("Location: lista.php"); exit; }

$stmt = $db->prepare("SELECT * FROM proyectos WHERE id = :id");
$stmt->execute([':id' => $id]);
$p = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$p) { header("Location: lista.php"); exit; }

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre']);
    if (!$nombre) {
        $error = 'El nombre es requerido.';
    } else {
        $stmt = $db->prepare("UPDATE proyectos SET nombre=:nombre, descripcion=:desc, cliente_id=:cliente, gerente_id=:gerente, estado=:estado, prioridad=:prioridad, fecha_inicio=:inicio, fecha_fin=:fin, presupuesto=:presupuesto, porcentaje_avance=:avance WHERE id=:id");
        $stmt->execute([
            ':nombre' => $nombre, ':desc' => $_POST['descripcion'],
            ':cliente' => $_POST['cliente_id'] ?: null,
            ':gerente' => $_POST['gerente_id'] ?: null,
            ':estado' => $_POST['estado'], ':prioridad' => $_POST['prioridad'],
            ':inicio' => $_POST['fecha_inicio'] ?: null,
            ':fin' => $_POST['fecha_fin'] ?: null,
            ':presupuesto' => $_POST['presupuesto'] ?: 0,
            ':avance' => $_POST['porcentaje_avance'] ?: 0,
            ':id' => $id
        ]);
        header("Location: ver.php?id=$id&msg=actualizado");
        exit;
    }
}

$clientes = $db->query("SELECT id, nombre FROM clientes ORDER BY nombre")->fetchAll(PDO::FETCH_ASSOC);
$usuarios = $db->query("SELECT id, nombre, apellido FROM usuarios WHERE activo=1 ORDER BY nombre")->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="page-header">
    <div>
        <div class="page-title">Editar Proyecto</div>
        <div class="page-subtitle"><?= htmlspecialchars($p['nombre']) ?></div>
    </div>
    <a href="ver.php?id=<?= $id ?>" class="btn btn-ghost">← Volver</a>
</div>

<?php if ($error): ?><div class="alert alert-error">✕ <?= $error ?></div><?php endif; ?>

<div class="card">
    <form method="POST">
        <div class="form-grid">
            <div class="form-group full">
                <label>Nombre del Proyecto *</label>
                <input type="text" name="nombre" value="<?= htmlspecialchars($p['nombre']) ?>">
            </div>
            <div class="form-group full">
                <label>Descripción</label>
                <textarea name="descripcion"><?= htmlspecialchars($p['descripcion']) ?></textarea>
            </div>
            <div class="form-group">
                <label>Cliente</label>
                <select name="cliente_id">
                    <option value="">— Sin cliente —</option>
                    <?php foreach ($clientes as $c): ?>
                        <option value="<?= $c['id'] ?>" <?= $p['cliente_id'] == $c['id'] ? 'selected' : '' ?>><?= htmlspecialchars($c['nombre']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Gerente</label>
                <select name="gerente_id">
                    <option value="">— Sin asignar —</option>
                    <?php foreach ($usuarios as $u): ?>
                        <option value="<?= $u['id'] ?>" <?= $p['gerente_id'] == $u['id'] ? 'selected' : '' ?>><?= htmlspecialchars($u['nombre'] . ' ' . $u['apellido']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Estado</label>
                <select name="estado">
                    <?php foreach (['pendiente','en_progreso','completado','cancelado'] as $e): ?>
                        <option value="<?= $e ?>" <?= $p['estado'] == $e ? 'selected' : '' ?>><?= str_replace('_', ' ', ucfirst($e)) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Prioridad</label>
                <select name="prioridad">
                    <?php foreach (['alta','media','baja'] as $pr): ?>
                        <option value="<?= $pr ?>" <?= $p['prioridad'] == $pr ? 'selected' : '' ?>><?= ucfirst($pr) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Fecha Inicio</label>
                <input type="date" name="fecha_inicio" value="<?= $p['fecha_inicio'] ? date('Y-m-d', strtotime($p['fecha_inicio'])) : '' ?>">
            </div>
            <div class="form-group">
                <label>Fecha Fin</label>
                <input type="date" name="fecha_fin" value="<?= $p['fecha_fin'] ? date('Y-m-d', strtotime($p['fecha_fin'])) : '' ?>">
            </div>
            <div class="form-group">
                <label>Presupuesto ($)</label>
                <input type="number" name="presupuesto" step="0.01" min="0" value="<?= $p['presupuesto'] ?>">
            </div>
            <div class="form-group">
                <label>% Avance</label>
                <input type="number" name="porcentaje_avance" min="0" max="100" step="0.01" value="<?= $p['porcentaje_avance'] ?>">
            </div>
        </div>
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Guardar Cambios</button>
            <a href="ver.php?id=<?= $id ?>" class="btn btn-ghost">Cancelar</a>
        </div>
    </form>
</div>

<?php include("../includes/footer.php"); ?>
