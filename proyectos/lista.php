<?php
include("../includes/header.php");
requiereRol([ROL_ADMIN, ROL_GERENTE]);
include("../conexion.php");
$db = (new Cconexion())->conexionBD();

// Filtros
$filtro_estado = $_GET['estado'] ?? '';
$filtro_prioridad = $_GET['prioridad'] ?? '';

$where = "WHERE 1=1";
$params = [];
if ($filtro_estado) { $where .= " AND p.estado = :estado"; $params[':estado'] = $filtro_estado; }
if ($filtro_prioridad) { $where .= " AND p.prioridad = :prioridad"; $params[':prioridad'] = $filtro_prioridad; }

$sql = "SELECT p.*, c.nombre AS cliente,
        u.nombre + ' ' + u.apellido AS gerente,
        (SELECT ISNULL(SUM(monto),0) FROM gastos WHERE proyecto_id = p.id) AS total_gastos,
        (SELECT ISNULL(SUM(horas_trabajadas),0) FROM registro_tiempos rt JOIN tareas t ON rt.tarea_id = t.id WHERE t.proyecto_id = p.id) AS total_horas
        FROM proyectos p
        LEFT JOIN clientes c ON p.cliente_id = c.id
        LEFT JOIN usuarios u ON p.gerente_id = u.id
        $where ORDER BY p.creado_en DESC";

$stmt = $db->prepare($sql);
$stmt->execute($params);
$proyectos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="page-header">
    <div>
        <div class="page-title">Proyectos</div>
        <div class="page-subtitle"><?= count($proyectos) ?> proyecto(s) encontrado(s)</div>
    </div>
    <a href="crear.php" class="btn btn-primary">＋ Nuevo Proyecto</a>
</div>

<!-- Filtros -->
<div class="card" style="margin-bottom:20px;padding:16px 24px;">
    <form method="GET" style="display:flex;gap:12px;align-items:flex-end;flex-wrap:wrap;">
        <div class="form-group" style="margin:0;min-width:160px;">
            <label>Estado</label>
            <select name="estado">
                <option value="">Todos</option>
                <option value="pendiente" <?= $filtro_estado=='pendiente'?'selected':'' ?>>Pendiente</option>
                <option value="en_progreso" <?= $filtro_estado=='en_progreso'?'selected':'' ?>>En Progreso</option>
                <option value="completado" <?= $filtro_estado=='completado'?'selected':'' ?>>Completado</option>
                <option value="cancelado" <?= $filtro_estado=='cancelado'?'selected':'' ?>>Cancelado</option>
            </select>
        </div>
        <div class="form-group" style="margin:0;min-width:140px;">
            <label>Prioridad</label>
            <select name="prioridad">
                <option value="">Todas</option>
                <option value="alta" <?= $filtro_prioridad=='alta'?'selected':'' ?>>Alta</option>
                <option value="media" <?= $filtro_prioridad=='media'?'selected':'' ?>>Media</option>
                <option value="baja" <?= $filtro_prioridad=='baja'?'selected':'' ?>>Baja</option>
            </select>
        </div>
        <button type="submit" class="btn btn-ghost">Filtrar</button>
        <a href="lista.php" class="btn btn-ghost">Limpiar</a>
    </form>
</div>

<div class="card">
    <div class="table-wrap">
        <?php if (empty($proyectos)): ?>
            <div class="empty-state">◉ <p>No hay proyectos que mostrar</p></div>
        <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nombre</th>
                    <th>Cliente</th>
                    <th>Gerente</th>
                    <th>Estado</th>
                    <th>Prioridad</th>
                    <th>Avance</th>
                    <th>Presupuesto</th>
                    <th>Gasto</th>
                    <th>Horas</th>
                    <th>Fecha Fin</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($proyectos as $p): ?>
                <tr>
                    <td style="color:var(--text3)"><?= $p['id'] ?></td>
                    <td><a href="ver.php?id=<?= $p['id'] ?>" style="color:var(--text);font-weight:500;text-decoration:none;"><?= htmlspecialchars($p['nombre']) ?></a></td>
                    <td><?= htmlspecialchars($p['cliente'] ?? '—') ?></td>
                    <td><?= htmlspecialchars($p['gerente'] ?? '—') ?></td>
                    <td><span class="badge badge-<?= $p['estado'] ?>"><?= str_replace('_', ' ', $p['estado']) ?></span></td>
                    <td><span class="badge badge-<?= $p['prioridad'] ?>"><?= $p['prioridad'] ?></span></td>
                    <td>
                        <div style="display:flex;align-items:center;gap:8px;">
                            <div class="progress-wrap"><div class="progress-bar" style="width:<?= $p['porcentaje_avance'] ?>%"></div></div>
                            <span style="font-size:11px;color:var(--text3)"><?= $p['porcentaje_avance'] ?>%</span>
                        </div>
                    </td>
                    <td>$<?= number_format($p['presupuesto'], 2) ?></td>
                    <td style="color:<?= $p['total_gastos'] > $p['presupuesto'] ? 'var(--accent2)' : 'var(--text2)' ?>">$<?= number_format($p['total_gastos'], 2) ?></td>
                    <td><?= number_format($p['total_horas'], 1) ?>h</td>
                    <td><?= $p['fecha_fin'] ? date('d/m/Y', strtotime($p['fecha_fin'])) : '—' ?></td>
                    <td>
                        <div style="display:flex;gap:6px;">
                            <a href="ver.php?id=<?= $p['id'] ?>" class="btn btn-ghost btn-sm">Ver</a>
                            <a href="editar.php?id=<?= $p['id'] ?>" class="btn btn-ghost btn-sm">Editar</a>
                            <a href="eliminar.php?id=<?= $p['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Eliminar proyecto?')">Eliminar</a>
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
