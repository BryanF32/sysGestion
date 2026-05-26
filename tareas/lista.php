<?php
include("../includes/header.php");
include("../conexion.php");
$db = (new Cconexion())->conexionBD();

$filtro_estado = $_GET['estado'] ?? '';
$filtro_proyecto = $_GET['proyecto_id'] ?? '';

$where = "WHERE 1=1";
$params = [];
if ($filtro_estado) { $where .= " AND t.estado = :estado"; $params[':estado'] = $filtro_estado; }
if ($filtro_proyecto) { $where .= " AND t.proyecto_id = :pid"; $params[':pid'] = $filtro_proyecto; }

$tareas = $db->prepare("SELECT t.*, p.nombre AS proyecto, u.nombre + ' ' + u.apellido AS asignado
    FROM tareas t
    LEFT JOIN proyectos p ON t.proyecto_id = p.id
    LEFT JOIN usuarios u ON t.asignado_a = u.id
    $where ORDER BY t.creado_en DESC");
$tareas->execute($params);
$tareas = $tareas->fetchAll(PDO::FETCH_ASSOC);

$proyectos = $db->query("SELECT id, nombre FROM proyectos ORDER BY nombre")->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="page-header">
    <div>
        <div class="page-title">Tareas</div>
        <div class="page-subtitle"><?= count($tareas) ?> tarea(s)</div>
    </div>
    <a href="crear.php" class="btn btn-primary">＋ Nueva Tarea</a>
</div>

<div class="card" style="margin-bottom:20px;padding:16px 24px;">
    <form method="GET" style="display:flex;gap:12px;align-items:flex-end;flex-wrap:wrap;">
        <div class="form-group" style="margin:0;min-width:180px;">
            <label>Proyecto</label>
            <select name="proyecto_id">
                <option value="">Todos</option>
                <?php foreach ($proyectos as $pr): ?>
                    <option value="<?= $pr['id'] ?>" <?= $filtro_proyecto == $pr['id'] ? 'selected' : '' ?>><?= htmlspecialchars($pr['nombre']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group" style="margin:0;min-width:140px;">
            <label>Estado</label>
            <select name="estado">
                <option value="">Todos</option>
                <option value="pendiente" <?= $filtro_estado=='pendiente'?'selected':'' ?>>Pendiente</option>
                <option value="en_progreso" <?= $filtro_estado=='en_progreso'?'selected':'' ?>>En Progreso</option>
                <option value="completado" <?= $filtro_estado=='completado'?'selected':'' ?>>Completado</option>
            </select>
        </div>
        <button type="submit" class="btn btn-ghost">Filtrar</button>
        <a href="lista.php" class="btn btn-ghost">Limpiar</a>
    </form>
</div>

<div class="card">
    <div class="table-wrap">
        <?php if (empty($tareas)): ?>
            <div class="empty-state">◎ <p>No hay tareas</p></div>
        <?php else: ?>
        <table>
            <thead>
                <tr><th>#</th><th>Tarea</th><th>Proyecto</th><th>Asignado</th><th>Estado</th><th>Prioridad</th><th>H. Est.</th><th>H. Real</th><th>Acciones</th></tr>
            </thead>
            <tbody>
            <?php foreach ($tareas as $t): ?>
                <tr>
                    <td style="color:var(--text3)"><?= $t['id'] ?></td>
                    <td style="color:var(--text);font-weight:500;"><?= htmlspecialchars($t['nombre']) ?></td>
                    <td><a href="../proyectos/ver.php?id=<?= $t['proyecto_id'] ?>" style="color:var(--accent);text-decoration:none;"><?= htmlspecialchars($t['proyecto'] ?? '—') ?></a></td>
                    <td><?= htmlspecialchars($t['asignado'] ?? '—') ?></td>
                    <td><span class="badge badge-<?= $t['estado'] ?>"><?= str_replace('_', ' ', $t['estado']) ?></span></td>
                    <td><span class="badge badge-<?= $t['prioridad'] ?>"><?= $t['prioridad'] ?></span></td>
                    <td><?= $t['horas_estimadas'] ?>h</td>
                    <td><?= $t['horas_reales'] ?>h</td>
                    <td>
                        <div style="display:flex;gap:6px;">
                            <a href="editar.php?id=<?= $t['id'] ?>" class="btn btn-ghost btn-sm">Editar</a>
                            <a href="eliminar.php?id=<?= $t['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Eliminar tarea?')">Eliminar</a>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>
</div>

<?php include("../includes/footer.php"); ?>
